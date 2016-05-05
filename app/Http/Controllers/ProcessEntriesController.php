<?php

namespace App\Http\Controllers;

use App\Process;
use App\Processentrycomment;
use App\Status;
use App\Processfile;
use Illuminate\Http\Request;

use App\Http\Requests\ProcessEntriesRequest;

use App\Processentry;

use Illuminate\Support\Facades\Auth;
use Storage, File;
use Illuminate\Support\Facades\Mail;

class ProcessEntriesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ProcessEntriesRequest $request, $id)
    {
        $statusId   = Status::select('id', 'title')
            ->where('company_id', $request->session()->get('companyId'))
            ->orderBy('sort_id', 'asc')
            ->get();
        return view('createProcessEntries',['statusIds' => $statusId])->with('id', $id);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProcessEntriesRequest $request)
    {
        $processEntry                = new Processentry;
        $processEntry->process_id    = $request->processId;
        $processEntry->title         = $request->title;
        $processEntry->description   = $request->description;
        $processEntry->confirmation  = $request->confirmation;
        $processEntry->comments_open = $request->comments;
        $processEntry->save();

        if($processEntry->id>0){
            $this->notifyEntry($processEntry->id, $request);
        }
        if($request->status>0)
        {
            Process::where('id',$request->processId)->update(['status_id' =>$request->status]);
        }

        return response()->json(['success' => true, 'entry' => $processEntry->id]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeFile(ProcessEntriesRequest $request, $id)
    {
        $files                             = $request->file('file');
        foreach($files as $file){
            $orginalName                   = $file->getClientOriginalName();
            $split_file                    = explode('.', $orginalName);
            $exploded_filename             = $split_file[0];
            $extension                     = $file->getClientOriginalExtension();

            /* Save process file details begin */
            $Processfile                   = new Processfile;
            $Processfile->process_entry_id = $request->entryID;
            $Processfile->title            = $exploded_filename;
            $Processfile->save();
            /* Save process file details end */
            $filename                      = $Processfile->id;
            if($filename>0){
                Storage::disk('local')->makeDirectory($request->entryID, 0777);
                Storage::disk('local')->put($request->entryID.'/'.$filename.'.'.$extension,  File::get($file));
                session()->flash('fileUploadMessage', 'File upload message');
            }
        }
        return response()->json(['success' => true, 'exploded_filename' => $exploded_filename, 'id' => $id]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getFileDetails(ProcessEntriesRequest $request)
    {
        $getProcessFileNames  = Processfile::select('id', 'title', 'description')
            ->where('id', $request->fileid)
            ->first();
        return response()->json(['success' => true, 'getProcessFileNames' => $getProcessFileNames]);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeFileDetails(ProcessEntriesRequest $request, $eid, $fid)
    {
        Processfile::where('id', $fid)
            ->where('process_entry_id', $eid)
            ->update(['title' => $request->titleFileDetails, 'description' => $request->descriptionFileDetails]);

        return response()->json(['success' => true]);
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $listProcessEntries      = Processentry::select('id', 'title', 'description', 'process_id', 'created_at')
            ->where('process_id', $id)
            ->where('status','<>', 'deleted')
            ->orderBy('id', 'desc')
            ->get();
        return view('listProcessEntries', ['listProcessEntries' => $listProcessEntries, 'processId' => $id]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        $listProcessEntry      = Processentry::select('id', 'title', 'description', 'process_id', 'confirmation','comments_open', 'created_at')
            ->where('id', $id)
            ->first();

        $listProcessFileNames  = Processfile::select('id', 'title', 'description')
            ->where('process_entry_id', $id)
            ->get();

        return view('updateProcessEntries', ['listProcessEntry' => $listProcessEntry, 'listProcessFileNames' => $listProcessFileNames]);
    }

    /**
     * For download the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function fileDownload($id, $file)
    {
        /* For getting title from table */
        $getFileTitle = Processfile::select('title')
            ->where('id', $file)
            ->first();

        /* Download file*/
        $fileLists = Storage::disk('local')->files($id);
        foreach ($fileLists as $fileList){
            $fileExplodeSlash = explode('/', $fileList); // 7/35.pdf
            $fileExploded     = end($fileExplodeSlash); //35.pdf
            $fileExplode      = explode('.', $fileExploded);
            $fileExtension    = end($fileExplode); //pdf
            if($file == $fileExplode[0]){
                $filePath        = storage_path('app'). '/' .$id. '/' .$fileExploded; //C:\xampp\htdocs\cuin\storage\app/6/bsK2rF11Xx.jpg
                switch( $fileExtension ) {
                    case "gif": $ctype="image/gif"; break;
                    case "png": $ctype="image/png"; break;
                    case "jpeg":
                    case "jpg": $ctype="image/jpeg"; break;
                    case 'docx':
                    case 'doc': $ctype="application/msword"; break;
                    case "pdf": $ctype="application/pdf"; break;
                    case "psd": $ctype="image/vnd.adobe.photoshop"; break;
                    case "zip": $ctype="application/zip"; break;
                    default:
                }
                return response()->download($filePath, $getFileTitle->title.".".$fileExtension, ['Content-Type' => $ctype]);
            }
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ProcessEntriesRequest $request, $id)
    {
        $processEntryUpdate              = Processentry::find($id);
        $processEntryUpdate->title       = $request->title;
        $processEntryUpdate->description = $request->description;
        $processEntryUpdate->confirmation      = $request->confirmation;
        $processEntryUpdate->comments_open      = $request->comments;
        $processEntryUpdate->save();
        /* File storage ends */
        return redirect(url('customers/list/process/'.$processEntryUpdate->process_id.'/entries'))->with('updateProcessEntriesMessage', true);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateFile(ProcessEntriesRequest $request, $id)
    {
        if( $request->file('file')){
            $files                             = $request->file('file');
            foreach($files as $file){
                $orginalName                   = $file->getClientOriginalName();
                $split_file                    = explode('.', $orginalName);
                $exploded_filename[]           = $split_file[0];
                $extension                     = $file->getClientOriginalExtension();

                /* Save process file details begin */
                $Processfile                   = new Processfile;
                $Processfile->process_entry_id = $id;
                $Processfile->title            = $split_file[0];
                $Processfile->save();
                /* Save process file details end */

                $filename                      = $Processfile->id;
                if($filename > 0){
                    Storage::disk('local')->put($id.'/'.$filename.'.'.$extension,  File::get($file));
                }
            }
        }
        return response()->json(['success' => true, 'exploded_filename' => $exploded_filename, 'id' => $id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, $file)
    {
        $fileLists = Storage::disk('local')->files($id);
        foreach ($fileLists as $fileList){
            $fileExplode      = explode('.', $fileList); // 7/35.pdf
            $fileExtension    = end($fileExplode);
            $fileExplodeSlash = explode('/', $fileExplode[0]);
            $fileExploded     = end($fileExplodeSlash);
            if($file == $fileExploded){
                Storage::disk('local')->delete($id.'/'.$file.'.'.$fileExtension);
                $delete = Processfile::destroy($file);
            }
        }
        return redirect()->back()->with('message', 'File deleted successfully!');
    }

    /**
     * Notification email to customer - on adding a new entry
     * @param $entryid
     * @param $request
     */
    protected function notifyEntry($entryid, $request)
    {
        $newentry = Processentry::select('process_entries.title', 'processes.title AS process', 'processes.id AS pid', 'customers.name', 'customers.email', 'customers.hash', 'customers.active')
            ->join('processes','processes.id','=','process_entries.process_id')
            ->join('customers','customers.id','=','processes.customer_id')
            ->where('process_entries.id',$entryid)
            ->first();
        if($newentry->active=='yes') {
            try {
                Mail::send('emails.notifyEntry', ['processEntry' => $newentry], function ($message) use ($newentry) {
                    $message->to($newentry->email, $newentry->name)->subject('Notification: A new process entry created');
                });
            } catch (Exception $e) {
                $request->session()->flash('error', 'Notification mail not send!');
            }
        }
    }

    /**
     * Get entry file count
     * @param $id
     * @return int
     */
    public function getFileCount($id)
    {
        $dir       = storage_path('app').'/'.$id;
        $shwCount = '';
        if(Storage::exists('/'.$id)) {
            $contents = preg_grep('/^([^.])/', scandir($dir));
            if (count($contents) > 0) {
                $count = count($contents);
                $str   = ($count>1)?' files':' file';
                $shwCount = '<h5><small>'.$count. $str .'</small></h5>';
            }
        }
        return $shwCount;
    }

    /**
     * Delete process entry -Ajax
     * @param Request $request
     * @param $id
     * @return mixed
     */
    public function deleteEntry(Request $request, $id)
    {
        if(!$request->ajax()) {
            return response()->json(['mes'=>'bad request']);
        }
        Processentry::where('id',$id)->update(['status' => 'deleted']);
        return response()->json(['mes'=>'done']);
    }

    /**
     * show button to entry Comments page
     * @param $id
     * @return string
     */
    public function getCommentButton($id)
    {
        $entry = Processentry::find($id);
        $comments = $entry->comments->count();

        $btn = '<a href="'.url('/customers/list/process/entry/'.$id.'/comments').'" class="btn btn-info btn-sm" title="View comments">Comments  <span class="badge"> '.$comments.'</span></a>';

        return $btn;
    }

    /**
     * show entry comments
     * @param $id
     * @return mixed
     */
    public function showComments($id)
    {
        $entry = Processentry::find($id);
        $comments = Processentrycomment::select('id','content','created_at')
            ->where('process_entry_id',$id)
            ->orderBY('created_at','DESC')
            ->get();

        return view('listEntryComments',['entry' => $entry, 'comments' => $comments]);
    }

    /**
     * post comments - Ajax
     * @param Request $request
     * @return mixed
     */
    public function postComment(Request $request)
    {
        if(!$request->ajax())
        {
            return response()->json(['mes'=>'bad request']);
        }
        $comment = new Processentrycomment();
        $comment->process_entry_id = $request->entry_id;
        $comment->user_id = Auth::user()->id;
        $comment->content = $request->contents;
        $comment->save();
        if($comment->id>0){
            $this->notifyUserComment($comment->id, $request);
            $html = '<div class="well well-sm">' . $comment->content . '<h6><small>'.date('d.m.Y H:i', strtotime($comment->created_at)).'</small></h6></div>';
            return response()->json(['mes' => 'done', 'text'=>$html]);
        }
        else{
            return response()->json(['mes' => 'Error, comment not saved!']);
        }

    }

    /**
     * Notification email to customer when user posted a new comment.
     * @param $id
     * @param $request
     */
    protected function notifyUserComment($id, $request)
    {
        $comment = Processentrycomment::select('process_entries.title', 'processes.id AS pid', 'customers.name', 'customers.email', 'customers.hash', 'customers.active', 'process_entry_comments.content')
            ->join('process_entries','process_entries.id','=','process_entry_comments.process_entry_id')
            ->join('processes','processes.id','=','process_entries.process_id')
            ->join('customers','customers.id','=','processes.customer_id')
            ->where('process_entry_comments.id',$id)
            ->first();
        if($comment->active=='yes') {
            try {
                Mail::send('emails.notifyUserComment', ['comment' => $comment], function ($message) use ($comment) {
                    $message->to($comment->email, $comment->name)->subject('Notification: Admin has posted a new comment');
                });
            } catch (Exception $e) {
                $request->session()->flash('error', 'Notification mail not send!');
            }
        }
    }

}

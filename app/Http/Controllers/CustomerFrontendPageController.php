<?php

namespace App\Http\Controllers;

use App\Customer;
use App\Processentry;
use App\Processentrycomment;
use App\Processentryhistory;
use App\Processfile;
use Illuminate\Http\Request;

use App\Http\Requests;

use App\Process;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\File\File;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class CustomerFrontendPageController extends Controller
{
    /**
     * CustomerFrontendPageController constructor.
     */
    public function __construct(){
        $this->middleware('customer');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($hash, Request $request)
    {
        $id = Auth::guard('customer')->user()->id;
        $customerProcessDetails = Process::select('processes.id','processes.title', 'processes.description', 'customers.name', 'statuses.light', 'processes.created_at')
            ->join('customers', 'processes.customer_id', '=', 'customers.id')
            ->join('statuses', 'processes.status_id', '=', 'statuses.id')
            ->where('processes.status', '<>', 'deleted')
            ->where('customers.hash', $hash)
            ->where('customers.id', $id)
            ->orderBy('id', 'ASC')
            ->first();

        if(isset($request->pid) && $request->pid >0) {
            $customerProcessDetails = Process::select('processes.id','processes.title', 'processes.description', 'customers.name', 'statuses.light', 'processes.created_at')
                ->join('customers', 'processes.customer_id', '=', 'customers.id')
                ->join('statuses', 'processes.status_id', '=', 'statuses.id')
                ->where('processes.status', '<>', 'deleted')
                ->where('customers.hash', $hash)
                ->where('customers.id', $id)
                ->where('processes.id', $request->pid)
                ->orderBy('id', 'ASC')
                ->first();
        }
        $allProcesses   = '';
        $processEntries = '';
        $notice         = '';
        if(count($customerProcessDetails)>0) {
            $process_id = $customerProcessDetails->id;

            $allProcesses = Process::select('processes.id', 'processes.title', 'processes.reference_id', 'customers.hash')
                ->join('customers', 'processes.customer_id', '=', 'customers.id')
                ->join('statuses', 'processes.status_id', '=', 'statuses.id')
                ->where('processes.status', '<>', 'deleted')
                ->where('customers.hash', $hash)
                ->where('customers.id', $id)
                ->orderBy('title', 'ASC')
                ->get();

            $processEntries = Processentry::where('process_id', $process_id)
                ->where('status', '<>', 'deleted')
                ->orderBy('created_at', 'DESC')
                ->get();
        }
        else
        {
            $notice = Customer::select('notice_external')->find($id);
        }

        return view('customerFrontendPage', ['processDetails'=>$customerProcessDetails, 'processes'=>$allProcesses, 'customerProcessEntries' => $processEntries, 'notice' => $notice]);
    }

    /**
     * Get process entry Single image - from view
     * @param $id
     * @return string
     */
    public function getEntryImage($id)
    {
        $allowed    = array('jpg','jpeg','png');
        $entryImage = '';
        $images     = array();
        $dir      = storage_path('app').'/'.$id;
        if(Storage::exists('/'.$id)) {
            $pictures = preg_grep('/^([^.])/', scandir($dir));
            if (count($pictures) > 0) {
                foreach ($pictures as $file) {
                    $extn = strtolower(explode(".", $file)[1]);
                    if (in_array($extn, $allowed)) {
                        $images[] = $file;
                    }
                }
            }
            if (count($images) == 1)
                $entryImage = '<img src="'. url('/entryImage/' . $id . '/' . $images[0]) . '" style="margin: 20px 0; display: block; border-radius: 0.25em;">';
        }

        return $entryImage;
    }

    /**
     * process image display
     * @param $path
     */
    public function showImage($path)
    {
        $extn = explode(".",$path)[1];
        if($extn =='jpg' || $extn =='jpeg') {
            $im = imagecreatefromjpeg(storage_path('app') . '/' . $path);

            header('Content-type: image/jpeg');
            imagejpeg($im);
            imagedestroy($im);
        }
        elseif ($extn =='png') {

            $im = imagecreatefrompng(storage_path('app') . '/' . $path);

            header('Content-type: image/jpeg');
            imagepng($im);
            imagedestroy($im);
        }
    }

    /**
     * Get all file list - to download - call from view
     * @param $id
     * @return string
     */
    public function getEntryFiles($id)
    {
        $filter    = array('jpg','jpeg','png');            
        $dir      = storage_path('app').'/'.$id;
        $files    = array();
        $list ='';
        if(Storage::exists('/'.$id)) {
            $contents = preg_grep('/^([^.])/', scandir($dir));
            if (count($contents) > 0) {
                foreach ($contents as $file) {
                    $extn = strtolower(explode(".", $file)[1]);
                    if (!in_array($extn, $filter)) {
                        $files[] = array(
                            'name'=>$file,
                            'size'=>$this->formatBytes(Storage::size('/'.$id.'/'.$file)),
                            'extn'=>$extn
                        );
                    }
                }
            }

            if (count($files) >0) {
                $list ='<div class="list-group" style="margin: 20px 0;">';
                foreach ($files AS $item) {
                    $info = $this->getFileDetails($item["name"]);
                    switch ($item["extn"]){
                        case 'pdf':
                            $filetype ='<img src="/assets/img/icn_pdf.png" alt="pdf file" class="media-middle">';
                            break;
                        case 'doc':
                        case 'docx':
                            $filetype ='<img src="/assets/img/icn_word.png" alt="word file" class="media-middle">';
                            break;
                        default:
                            $filetype ='<img src="/assets/img/icn_file.png" alt="text file" class="media-middle">';
                    }
                    $file_name =$info->title;
                    $file_desc ='';
                    if($info->description!='')
                        $file_desc = '<p class="list-group-item-text small"><h6><small>'.$info->description.'</small></h6></p>';
                    $list .= '<a href="'.url('/entry/download/'.$id.'/'.$item["name"]).'" class="list-group-item">'.$filetype.' '.$file_name.' ('.$item["size"].')
                    '.$file_desc.'</a>';

                }
                $list .='</div>';
            }
        }
        return $list;

    }

    /**
     * get file details from database
     * @param $file
     * @return mixed
     */
    protected function getFileDetails($file)
    {
        $fileid = explode(".",$file)[0];
        $file_detail = Processfile::select('title', 'description')->find($fileid);
        return $file_detail;
    }

    /**
     * process file download request
     * @param $filepath
     * @return mixed
     */
    public function getEntryDownload($filepath)
    {
        $explode  = explode("/",$filepath);
        $filename = $explode[1];
        $file = storage_path('app'). '/' .$filepath;
        $info = $this->getFileDetails($filename);
        $download_file = $info->title.'.'.explode(".",$filename)[1];

        $history = new Processentryhistory();
        $history->process_entry_id = $explode[0];
        $history->notice = 'Customer has downloaded file: '.$download_file;
        $history->type = 'download';
        $history->save();

        return response()->download($file,  $download_file);
    }

    /**
     * show multiple image -list- gallary - call from view
     * @param $id
     * @return string
     */
    public function getEntryGallery($id)
    {
        $allowed    = array('jpg','jpeg','png');
        $gallery    = '';
        $images     = array();
        $dir      = storage_path('app').'/'.$id;
        if(Storage::exists('/'.$id)) {
            $pictures = preg_grep('/^([^.])/', scandir($dir));
            if (count($pictures) > 0) {
                foreach ($pictures as $file) {
                    $extn = strtolower(explode(".", $file)[1]);
                    if (in_array($extn, $allowed)) {
                        $images[] = $file;
                    }
                }
            }
            if (count($images) >1) {
                foreach ($images AS $img) {
                    $gallery .= '<div class="col-md-4">
<a href="'.url('/entryImage/' . $id . '/' . $img).'" title="'.$img.'" data-gallery>
<img src="'.url('/entryImage/' . $id . '/' . $img) . '" width="100%">
</a></div>';
                }
                $gallery = '<div class="row" style="margin: 20px 0;">'.$gallery.'</div>';
            }
        }

        return $gallery;
    }

    /**
     * Show entry history- show actions based on confirmation flag.
     * @param $id
     * @return string
     */
    public function getEntryHistory($id)
    {
        $history = Processentryhistory:: where('process_entry_id',$id)
            ->where('type','')
            ->first();
        if(count($history)>0)
        {
            $class = 'text-danger';
            if(strstr($history->notice,'confirmed'))
                $class = 'text-success';
            return '<div class="'.$class.'">'.$history->notice.'<h6><small>'.date('d.m.Y H:i', strtotime($history->created_at)).'</small></h6></div>';
        }
        else{
            $confirmButtonLabel = trans('messages.confirmButtonLabel');
            $rejectButtonLabel = trans('messages.rejectButtonLabel');
            return '<div class="form-group">
<button class="btn btn-success btn-sm updateHist" id="confrm_'.$id.'">'.$confirmButtonLabel.'</button> 
<button class="btn btn-danger btn-sm updateHist" id="reject_'.$id.'">'.$rejectButtonLabel.'</button>
</div>';
        }
    }

    /**
     * user feedback - confirm/reject action
     * @param Request $request
     * @return mixed
     */
    public function updateHistory(Request $request)
    {
        if(!$request->ajax())
        {
            return response()->json(['mes'=>'<p class="text-danger">bad request</p>']);
        }
        $history = new Processentryhistory();
        $param  = explode("_",$request->entry_id);
        $notice = ($param[0]=='confrm')?'Customer has confirmed':'Customer has rejected';
        $history->process_entry_id = $param[1];
        $history->notice = $notice;
        $history->save();
        if($history->id>0) {
            $this->notifyConfirmation($history->id, $request);
            $class = 'text-danger';
            $display = trans('messages.rejectedMessage');
            if(strstr($history->notice,'confirmed')){
                $display = trans('messages.confirmMessage');
                $class = 'text-success';
            }

            return response()->json(['mes' => '<div class="'.$class.'">' . $display . '<h6><small>'.date('d.m.Y H:i', strtotime($history->created_at)).'</small></h6></div>']);
        }
        else
            return response()->json(['mes'=>'<div class="text-danger">Error, please try again</div>']);
    }

    /**
     * Show comment box- based on comments-open flag
     * @param $id
     * @return string
     */
    public function getEntryComments($id)
    {
        $comments = Processentrycomment::where('process_entry_id',$id)->orderBy('created_at', 'DESC')->get();
        $html ='';
        if(count($comments)>0) {
            foreach ($comments as $comment) {
                if($comment->content!='') {
                    $tag ='';
                    if($comment->user_id>0)
                        $tag =', <span class="small" style="font-style: italic;">by Admin</span>';
                    $html .= '<div class="well well-sm">' . $comment->content . '<h6><small>' . date('d.m.Y H:i', strtotime($comment->created_at)) . '</small>'. $tag .'</h6></div>';
                }
            }
        }
        $textareaCommentPlaceholder = trans('messages.textareaCommentPlaceholder');
        $disabledPostButtonTitle = trans('messages.disabledPostButtonTitle');
        $html .= '<div class="form-group" id="container_'.$id.'">
<textarea class="form-control txt-comment"  name="contents" id="contents_'.$id.'" placeholder="'.$textareaCommentPlaceholder.'"></textarea>
</div>
<div class="form-group">
<button class="btn btn-primary btn-sm pull-right disabled commentBtn" id="cmtPost_'.$id.'" disabled="disabled" title="'.$disabledPostButtonTitle.'">Post</button>
</div>';

        return $html;
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
        $comment->content = $request->contents;
        $comment->save();
        if($comment->id>0){
            $this->notifyComment($comment->id, $request);
            $html = '<div class="well well-sm">' . $comment->content . '<h6><small>'.date('d.m.Y H:i', strtotime($comment->created_at)).'</small></h6></div>';
            return response()->json(['mes' => 'done', 'text'=>$html]);
        }
        else{
            return response()->json(['mes' => 'Error, comment not saved!']);
        }

    }

    /**
     * Notification email to User - on Customer confirm/reject from timeline
     * @param $id
     * @param $request
     */
    protected function notifyConfirmation($id, $request)
    {
        $param  = explode("_",$request->entry_id);
        $confirm = Processentry::select('process_entries.title', 'customers.name', 'customers.email', 'users.email AS emailto', 'users.name AS nameto')
            ->join('processes','processes.id','=','process_entries.process_id')
            ->join('users','users.company_id','=','processes.company_id')
            ->join('customers','customers.id','=','processes.customer_id')
            ->where('process_entries.id',$param[1])
            ->first();
        try {
            Mail::send('emails.notifyEntryConfirm', ['confirmEntry' => $confirm, 'input'=> $param[0]], function ($message) use($confirm) {
                $message->to($confirm->emailto, $confirm->nameto)->subject('Notification: Customer set a confirmation');
            });
        } catch (Exception $e) {
            $request->session()->flash('error', 'Notification mail not send!');
        }
    }

    /**
     * Notification email to User - on customer post a comment on an entry
     * @param $id
     * @param $request
     */
    protected function notifyComment($id, $request)
    {
        $entryid  = $request->entry_id;
        $comment = Processentry::select('process_entries.id AS eid', 'process_entries.title', 'customers.name', 'customers.email', 'users.email AS emailto', 'users.name AS nameto')
            ->join('processes','processes.id','=','process_entries.process_id')
            ->join('users','users.company_id','=','processes.company_id')
            ->join('customers','customers.id','=','processes.customer_id')
            ->where('process_entries.id',$entryid)
            ->first();
        $commentbody = Processentrycomment::select('content')->find($id);
        try {
            Mail::send('emails.notifyEntryComment', ['commentedEntry' => $comment, 'commentbody'=>$commentbody], function ($message) use($comment) {
                $message->to($comment->emailto, $comment->nameto)->subject('Notification: Customer has commented on an entry');
            });
        } catch (Exception $e) {
            $request->session()->flash('error', 'Notification mail not send!');
        }
    }

    /**
     * customer password change - view
     * @return mixed
     */
    public function changePassword()
    {
        return view('customer.changePassword');
    }

    /**
     * Update customer password
     * @param Request $request
     * @return mixed
     */
    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'old_password' => 'required',
            'password' => 'required|confirmed|min:6',
        ]);
        $id = Auth::guard('customer')->user()->id;
        $customer = Customer::find($id);

        if(Hash::check($request->old_password, $customer->password)){
            $customer->password = Hash::make($request->password);
            $customer->save();
            return redirect(url('/customer/password/change'))->with('status','Password Changed Successfully');
        }
        else{
            $validator->errors()->add('old_password', 'Old password is incorrect!');
            return redirect('/customer/password/change')->withErrors($validator);
        }
    }

    /**
     * show file size units
     * @param $bytes
     * @param int $precision
     * @return string
     */
    protected function formatBytes($bytes, $precision = 2) {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        // Uncomment one of the following alternatives
         $bytes /= pow(1024, $pow);
         //$bytes /= (1 << (10 * $pow));

        return round($bytes, $precision) . ' ' . $units[$pow];
    }

}

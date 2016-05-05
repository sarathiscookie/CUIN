<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Auth;

use App\Process;
use App\Customer;
use App\Processentry;

use Illuminate\Support\Facades\Storage;

class SearchController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Backend - Search
     * @param Request $request
     * @return mixed
     */
    public function search(Request $request)
    {
        if(!$request->ajax()) {
            return response()->json(['result'=>'bad request']);
        }

        $keyword =  $request->key;
        $result_customers = $this->searchCustomers($keyword);
        $result_processes = $this->searchProcesses($keyword);
        $result_entries   = $this->searchProcessEntries($keyword);
        $results = $result_customers.$result_processes.$result_entries;

        return response()->json(['result'=>$results]);
    }

    /**
     * search customers
     * @param $keyword
     * @return string
     */
    protected function searchCustomers($keyword)
    {
        $result_customer ='';
        $company = Auth::user()->company_id;
        $customers             = Customer::select('id', 'name', 'email', 'reference_id')
            ->where('company_id', $company)
            ->where(function ($query) use($keyword) {
                $query->where('name', 'LIKE', '%'.$keyword.'%')
                    ->orWhere('email', 'LIKE', '%'.$keyword.'%')
                    ->orWhere('reference_id', 'LIKE', '%'.$keyword.'%');
            })
            ->orderBy('name')
            ->get();
        if(count($customers)>0) {
            $result_customer = '<div class="list-group"><h5>CUSTOMERS ('.count($customers).')</h5>';
            foreach ($customers as $customer) {
                $result_customer .= '<a class="list-group-item" href="'.url('/customers/list/process/'.$customer->id).'">
<h5 class="list-group-item-heading"><strong>'.title_case($customer->name).' ( '.$customer->reference_id.' )</strong></h5>
<p class="list-group-item-text">'.$customer->email.'</p>
</a>';
            }
            $result_customer .='</div>';
        }
        return  $result_customer;
    }

    /**
     * Search processes
     * @param $keyword
     * @return string
     */
    protected function searchProcesses($keyword)
    {
        $result_process ='';
        $company = Auth::user()->company_id;
        $processes    = Process::select('id', 'title', 'description', 'reference_id')
            ->where('company_id', $company)
            ->where(function ($query) use($keyword) {
                $query->where('title', 'LIKE', '%'.$keyword.'%')
                    ->orWhere('description', 'LIKE', '%'.$keyword.'%')
                    ->orWhere('reference_id', 'LIKE', '%'.$keyword.'%');
            })
            ->orderBy('title')
            ->get();
        if(count($processes)>0) {
            $result_process = '<div class="list-group"><h5>PROCESSES ('.count($processes).')</h5>';
            foreach ($processes as $process) {
                $result_process .= '<a class="list-group-item" href="'.url('/customers/list/process/'.$process->id.'/entries').'">
<h5 class="list-group-item-heading"><strong>'.title_case($process->title).' ( '.$process->reference_id.' )</strong></h5>
<p class="list-group-item-text">'.$this->truncateText($process->description,150).'</p>
</a>';
            }
            $result_process .='</div>';
        }
        return  $result_process;
    }

    /**
     * Search process entries
     * @param $keyword
     * @return string
     */
    protected function searchProcessEntries($keyword)
    {
        $result_entry ='';
        $company = Auth::user()->company_id;
        $processEntries    = Processentry::select('process_entries.id', 'process_entries.title', 'process_entries.description')
            ->join('processes','processes.id','=','process_entries.process_id')
            ->where('processes.company_id', $company)
            ->where(function ($query) use($keyword) {
                $query->where('process_entries.title', 'LIKE', '%'.$keyword.'%')
                    ->orWhere('process_entries.description', 'LIKE', '%'.$keyword.'%');
            })
            ->orderBy('process_entries.title')
            ->get();

        if(count($processEntries)>0) {
            $result_entry = '<div class="list-group"><h5>PROCESS ENTRIES ('.count($processEntries).')</h5>';
            foreach ($processEntries as $entry) {
                $fileCount = $this->getFileCount($entry->id);
                $shwCount  ='';
                if($fileCount>0)
                    $shwCount = ' ( '.$fileCount. $str=(($fileCount>1)?' files':' file').' )';
                $result_entry .= '<a class="list-group-item" href="'.url('/customers/list/process/entries/'.$entry->id).'">
<h5 class="list-group-item-heading"><strong>'.title_case($entry->title).$shwCount.'</strong></h5>
<p class="list-group-item-text">'.$this->truncateText($entry->description,150).'</p>
</a>';
            }
            $result_entry .='</div>';
        }
        return  $result_entry;
    }

    /**
     * Get entry file count
     * @param $id
     * @return int
     */
    protected function getFileCount($id)
    {
        $dir   = storage_path('app').'/'.$id;
        $count = 0;
        if(Storage::exists('/'.$id)) {
            $contents = preg_grep('/^([^.])/', scandir($dir));
            if (count($contents) > 0) {
                $count =count($contents);
            }
        }
        return $count;
    }

    /**
     * Truncate Text with given number of characters
     * @param $string
     * @param $limit
     * @param string $break
     * @param string $pad
     * @return string
     */
    protected function truncateText($string, $limit, $break=".", $pad="...")
    {
        // return with no change if string is shorter than $limit
        if(strlen($string) <= $limit) return $string;

        // is $break present between $limit and the end of the string?
        if(false !== ($breakpoint = strpos($string, $break, $limit))) {
            if($breakpoint < strlen($string) - 1) {
                $string = substr($string, 0, $breakpoint) . $pad;
            }
        }
        return $string;
    }
}

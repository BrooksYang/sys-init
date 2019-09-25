<?php

namespace App\Http\Controllers\User;

use App\Traits\Children;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TraderIncomeController extends Controller
{

    use Children;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $traders = User::getTraders();
        //dump($traders);
        $tree = $this->tree($traders);

        return view('user.traderLevelIndex',compact('tree'));
    }

    /**
     * 遍历节点树
     *
     * @param $traders
     * @param int $pid
     * @param string $flag
     * @param string $html
     * @return string
     */
    private function tree($traders, $pid = 0,  $flag='', $html = '')
    {
        $html .= '<ul'.($flag?'':' id="browser" class="filetree"').'>';
        foreach ($traders as $key=>$trader) {
            if ($trader->pid == $pid) {
                $html .= '<li uid="' . $trader->id . '" pid="'.$trader->pid.'" path="' . @$trader->path .($trader->id==88?'" class="collapsable"':''). '">
                <a href="javascript:void(0)" '.($pid == 0 ? "class='topOne'" : "").'onclick="nodeShow('.$trader->id.')">'
                    .($trader->username?:($trader->phone?:$trader->email)).'</a>';
                $html .= $this->tree($traders, $trader->id, true);
            }
        }
        $html .= '</ul>';

        return $html;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}

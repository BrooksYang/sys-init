<?php

namespace App\Http\Controllers\OtcLegalCurrency;

use App\Http\Requests\LegalCurrencyRequest;
use App\Traits\ImgCrop;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

/**
 * 系统法币汇率管理
 *
 * Class LegalCurrencyController
 * @package App\Http\Controllers\Config
 */
class LegalCurrencyController extends Controller
{

    use ImgCrop;

    public static function type()
    {
        return [
            1 => ['name' => '是' ,'class' => 'success'],
            2 => ['name' => '否' ,'class' => 'default'],
        ];
    }

    /**
     * 系统法币信息列表
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $type = self::type();

        // 法币名称检索
        $search = trim($request->search,'');
        $orderC = trim($request->orderC ?:'desc','');

        $legalCurrency = \DB::table('legal_currencies as legal')
            ->when($search, function ($query) use ($search){
                return $query->where('legal.name','like',"%$search%")
                    ->orwhere('legal.abbr','like',"%$search%");
            })
            ->when($orderC, function ($query) use ($orderC){
                return $query->orderBy('legal.created_at', $orderC);
            })
            ->select('legal.*')
            ->paginate(config('app.pageSize') );

        return view('OtcLegalCurrency.legalCurrencyIndex', compact('legalCurrency','search', 'type'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $type = self::type();

        return view('OtcLegalCurrency.legalCurrencyCreate', compact('type'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param LegalCurrencyRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(LegalCurrencyRequest $request)
    {
        $legalCurrency = $request->except(['_token','editFlag','files', 'x','y','w','h']);
        $legalCurrency['created_at'] = self::carbonNow();

        $legalCurrency['flag'] = 'storage/flag/'.$request->flag;
        if ($request->hasFile('flag')) {
            $news['flag'] = $request->file('flag')->store('public/flag');
        }

        \DB::table('legal_currencies')->insert($legalCurrency);

        return redirect('otc/legalCurrency');
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
        $legalCurrency = \DB::table('legal_currencies')->where('id',$id)->first();

        if (empty($legalCurrency)) { return redirect('cms/news'); }

        return view('OtcLegalCurrency.legalCurrencyCreate',[
            'editFlag' => true,
            'type' =>  $type = self::type(),
            'flag' => $legalCurrency
        ]);
    }

    /**
     *  Update the specified resource in storage.
     *
     * @param LegalCurrencyRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */

    public function update(LegalCurrencyRequest $request, $id)
    {
        $legalCurrency = $request->except(['_token','_method','editFlag', 'files','x','y','w','h']);
        $legalCurrency['updated_at'] = self::carbonNow();

        $legalCurrency['flag'] = 'storage/flag/'.$request->flag;
        if ($request->hasFile('flag')) {
            $news['flag'] = $request->file('flag')->store('public/flag');
        }

        if (!$request->flag) {
            unset($legalCurrency['flag']);
        }

        \DB::table('legal_currencies')->where('id', $id)->update($legalCurrency);

        return redirect('otc/legalCurrency');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (\DB::table('legal_currencies')->where('id', $id)->delete()) {

            return response()->json([]);
        }
    }

    /**
     * 获取系统当前基准法币
     *
     * @return mixed
     */
    public function geSysLegalCurrency()
    {
        $sysLegal = \DB::table('configs')
            ->where('key','base_legal_currency')
            ->value('value');

        return $sysLegal;
    }

    /**
     * 上传国旗图标
     *
     * @param $dir
     */
    public function upload($dir)
    {
        $this->imgUpload(
            base64_decode($dir),
            config('imgCrop.flag.upload.max_width'),
            config('imgCrop.flag.upload.max_height'),
            config('imgCrop.flag.upload.min_width'),
            config('imgCrop.flag.upload.min_height')
        );
    }

    /**
     * 裁剪国旗图标
     *
     * @param $dir
     * @return \Illuminate\Http\JsonResponse
     */
    public function crop($dir)
    {
        return $this->imgCrop('width', 'height','imageUploadPreviewWidth', base64_decode($dir), 'cropImg');
    }
}

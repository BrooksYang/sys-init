<?php

namespace App\Http\Controllers\Wallet;

use App\Http\Requests\FinanceSubjectRequest;
use App\Models\Currency;
use App\Models\Wallet\FinanceSubject;
use App\Models\Wallet\WalletExternal;
use App\Models\Wallet\WalletTransaction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

/**
 * 收益科目管理
 *
 * Class OtcFinanceSubjectController
 * @package App\Http\Controllers\Wallet
 */
class OtcFinanceSubjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // 条件搜索
        $search = trim($request->search,'');
        $orderC = trim($request->orderC ?:'desc','');

        $currencies = Currency::getCurrencies();
        $external = WalletExternal::status(WalletExternal::ENABLE)->get();

        $subject = FinanceSubject::when($search, function ($query) use ($search){
                $query->where('title','like', "%$search%");
            })
            ->when($orderC, function ($query) use ($orderC) {
                return $query->orderBy('created_at', $orderC);
            })
            ->paginate(config('app.pageSize'));

        return view('wallet.financeSubjectIndex', compact('subject','search','currencies','external'));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param FinanceSubjectRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(FinanceSubjectRequest $request)
    {
        FinanceSubject::create([
            'title' => $request->title,
            'desc' => $request->desc,
        ]);

        return back();
    }


    /**
     * Update the specified resource in storage.
     *
     * @param FinanceSubjectRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(FinanceSubjectRequest $request, $id)
    {
        $subject = FinanceSubject::findOrFail($id);

        $subject->title = $request->title;
        $subject->desc = $request->desc;

        $subject->save();

        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function destroy($id)
    {
        // 已被使用的科目将替换为默认的“其它”科目
        $subject = FinanceSubject::findOrFail($id);

        DB::transaction(function () use ($subject) {
            // 默认科目是否存在
            $default = FinanceSubject::where('title', '其它')->orWhere('title','其他')->first();

            if (!$default) {
                $default = FinanceSubject::create(['title' => '其它', 'desc' => '系统默认科目']);
            }

            // 删除科目并替换原科目为默认科目
            WalletTransaction::subject($subject->id)->update(['subject_id' => $default->id]);

            $subject->delete();
        });

        return response()->json(['code' => 0, 'msg' => '删除成功']);
    }

}

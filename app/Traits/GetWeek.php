<?php

namespace App\Traits;


/**
 * 按年获取每周的开始和结束日期
 *
 * Trait GetWeek
 * @package App\Traits
 */
trait GetWeek {

    /**
    *
    * 获取一年每周的开始日期和结束日期
    *
    * @param $year
    * @return mixed
    */
    public static function getWeek($year)
    {
        $year_start = $year . "-01-01";
        $year_end = $year . "-12-31";
        $startday = strtotime($year_start);
        if (intval(date('N', $startday)) != '1') {
            $startday = strtotime("next monday", strtotime($year_start)); //获取年第一周的日期
        }
        $year_mondy = date("Y-m-d", $startday); //获取年第一周的日期

        $endday = strtotime($year_end);
        if (intval(date('W', $endday)) == '7') {
            $endday = strtotime("last sunday", strtotime($year_end));
        }

        //$num = intval(date('W', $endday));可以获取当年以前的年份有多少周，如果是当年还没到12-31号$num为1
        $num = 52; //一年约52周
        for ($i = 1; $i <= $num; $i++) {
            $j = $i - 1;
            $start_date = date("Y-m-d", strtotime("$year_mondy $j week ")).' 00:00:00';

            $end_day = date("Y-m-d", strtotime("$start_date +6 day")).' 23:59:59';

            $week_array[$i] = array(
                $start_date, $end_day);
        }
        return $week_array;
    }


    public function getWeeks($year)
    {
        //获取当年第一天的日期
        $yearFirstDay = $year . '-01-01 00:00:00';
        $yearEndDay = $year . '-12-31 23:59:59';

        //查看第一天是星期几
        $week = date('N', strtotime($yearFirstDay));
        //当年第一周的开始时间和结束时间（开始时间不一定是星期一）
        $days = 8 - $week;
        $firstWeekendDay = date('Y-m-d H:i:s', strtotime($year . '-01-0' . $days . ' 23:59:59'));
        $weeks = array();
        $weeks[1]['week_start'] = strtotime($yearFirstDay);
        $weeks[1]['week_end'] = strtotime($year . '-01-0' . $days . ' 23:59:59');
        //组装一年中 完整的各周开始和结束时间戳
        $days2 = ((strtotime($yearEndDay) - $weeks[1]['week_end']) / 86400) / 7;
        $days3 = (int)floor($days2);
        $length = $days3 + 1;
        for ($i = 2; $i <= $length; $i++) {
            $weeks[$i]['week_start'] = 1 + $weeks[$i - 1]['week_end'];
            $weeks[$i]['week_end'] = 604800 + $weeks[$i - 1]['week_end'];
        }
        //组装最后一周的开始和结束时间
        $remainder = ((strtotime($yearEndDay) - $weeks[1]['week_end']) / 86400) % 7;
        if ($remainder > 0) {
            $data = array();
            $data['week_start'] = 1 + end($weeks)['week_end'];
            $data['week_end'] = ($remainder * 86400) + end($weeks)['week_end'];
            array_push($weeks, $data);
        }

        foreach ($weeks as $key => &$week) {
            $week['week_start'] = date('Y-m-d H:i:s', $week['week_start']);
            $week['week_end'] = date('Y-m-d H:i:s', $week['week_end']);
        }

        return $weeks;
    }


} 
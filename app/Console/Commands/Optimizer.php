<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use App\Helpers\MyHelper;

class Optimizer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:Optimizer {range?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Optimize rules for Campaigns, Services, Offers, Domains';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //current date
        $date = date("Y-m-d");
        $sql = "SET SESSION group_concat_max_len = 1000000";
        DB::select( DB::raw($sql), array());

        //get the groups of rules
        $sql = "SELECT 
                    rule_type, 
                    rule_type_id,
                    type,
                    country,
                    region,
                    city,
                    agent,
                    group_concat(id) as rule_ids, 
                    group_concat(weight) as rule_weights, 
                    sum(weight) total_weight 
                FROM rules 
                WHERE type != 'ip' AND active = 1 AND weight > 0 
                GROUP BY rule_type, rule_type_id, type, country, region, city, agent";

        $all_rules = DB::select( DB::raw($sql), array());

        //stats_summary_manage
        foreach($all_rules as $r) 
        {
            $the_rules = explode(",", $r['rule_ids']);
            $old_weights = $this->update_weights($r['rule_ids'], $r['rule_weights'], $r['total_weight']);

            $sql = "SELECT 
                        rule_id, 
                        (sum(ROUND(revenue)) + sum(visitors)) weight 
                    FROM stats_summary_manage 
                    WHERE date = '{$date}' 
                    AND rule_id IN ({$r['rule_ids']}) 
                    GROUP BY rule_id 
                    ORDER BY revenue DESC";

            $rule_stats = DB::select( DB::raw($sql), array());
            $rule_stats = MyHelper::rekey_array($rule_stats, 'rule_id');
            
            //check which ids are missing and add a default weight of 1
            foreach($the_rules as $id) {
                if(!isset($rule_stats[$id])) {
                    $rule_stats[$id] = array(
                        'rule_id' => $id,
                        'weight' => 1
                    );
                }
            }

            $rule_ids = implode(",", array_keys($rule_stats));
            $weights  = MyHelper::implode_on_field(",", $rule_stats, 'weight');
            $total_weight = array_sum(explode(",", $weights));
            $optimize_weights = $this->update_weights($rule_ids, $weights, $total_weight);

            $this->info("Group -> {$r['rule_type']} ({$r['rule_type_id']}) {$r['type']} {$r['country']} {$r['region']} {$r['city']} {$r['agent']} : Total - {$total_weight}");
            foreach($old_weights as $rule_id => $old_weight) {
                $this->info("Updating -> ID: {$rule_id} -> OLD: $old_weight -> NEW: {$optimize_weights[$rule_id]}");
            }
        }
    }

    public function update_weights($ids, $weights, $total_weight) 
    {
        $rule_ids = explode(",", $ids);
        $rule_weights = explode(",", $weights);
        $rules = array_combine($rule_ids, $rule_weights);

        $n_rules = array();
        foreach($rules as $r_id => $r_weight) {
            if($total_weight > 0) {
                $n_rules[$r_id] = round(($r_weight/$total_weight) * 100);
            } else {
                $n_rules[$r_id] = 1;
            }
        }

        return $n_rules;
    }
}

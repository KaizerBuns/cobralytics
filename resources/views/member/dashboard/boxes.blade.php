<?
use App\Helpers\TableMap;
use App\Helpers\MyHelper;
?>

<div class="col-sm-6 col-lg-2">
    <div class="panel media pad-all">
        <div class="media-left">
            <span class="icon-wrap icon-wrap-sm icon-circle bg-success">
            <i class="fa fa-user fa-2x"></i>
            </span>
        </div>
        <div class="media-body">
            <p class="text-2x mar-no text-thin">{{ isset($today->visitors) ? TableMap::value_format('number', $today->visitors) : 0 }}</p>
            <p class="text-muted mar-no">Visitors Today</p>
        </div>
    </div>
</div>
<div class="col-sm-6 col-lg-2">
    <div class="panel media pad-all">
        <div class="media-left">
            <span class="icon-wrap icon-wrap-sm icon-circle bg-info">
            <i class="fa fa-hand-o-down fa-2x"></i>
            </span>
        </div>
        <div class="media-body">
            <p class="text-2x mar-no text-thin">{{ isset($today->clicks) ? TableMap::value_format('number', $today->clicks) : 0 }}</p>
            <p class="text-muted mar-no">Clicks Today</p>
        </div>
    </div>
</div>
<div class="col-sm-6 col-lg-2">
    <div class="panel media pad-all">
        <div class="media-left">
            <span class="icon-wrap icon-wrap-sm icon-circle bg-dark">
            <i class="fa fa-hand-o-right fa-2x"></i>
            </span>
        </div>
        <div class="media-body">
            <p class="text-2x mar-no text-thin">{{ isset($today->visitors) && $today->visitors ? round(($today->clicks/$today->visitors) * 100, 2) : 0 }}%</p>
            <p class="text-muted mar-no">CTR Today</p>
        </div>
    </div>
</div>
<div class="col-sm-6 col-lg-2">
    <div class="panel media pad-all">
        <div class="media-left">
            <span class="icon-wrap icon-wrap-sm icon-circle bg-warning">
            <i class="fa fa-user fa-2x"></i>
            </span>
        </div>
        <div class="media-body">
            <p class="text-2x mar-no text-thin">{{ isset($today->uniques) ? TableMap::value_format('number', $today->uniques) : 0 }}</p>
            <p class="text-muted mar-no">Uniques Today</p>
        </div>
    </div>
</div>
<div class="col-sm-6 col-lg-2">
    <div class="panel media pad-all">
        <div class="media-left">
            <span class="icon-wrap icon-wrap-sm icon-circle bg-danger">
            <i class="fa fa-dollar fa-2x"></i>
            </span>
        </div>
        <div class="media-body">
            <p class="text-2x mar-no text-thin">{{ isset($today->revenue) ? TableMap::value_format('money', $today->revenue) : 0 }}</p>
            <p class="text-muted mar-no">Revenue Today</p>
        </div>
    </div>
</div>
<div class="col-sm-6 col-lg-2">
    <div class="panel media pad-all">
        <div class="media-left">
            <span class="icon-wrap icon-wrap-sm icon-circle bg-mint">
            <i class="fa fa-shopping-cart fa-2x"></i>
            </span>
        </div>
        <div class="media-body">
            <p class="text-2x mar-no text-thin">{{ isset($today->conversion) ? TableMap::value_format('number', $today->conversion) : 0 }}</p>
            <p class="text-muted mar-no">Sales Today</p>
        </div>
    </div>
</div>
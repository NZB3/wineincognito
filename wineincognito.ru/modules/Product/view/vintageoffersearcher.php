<?php 
    langSetDefault('product', 'offer searcher');
    if(!isset($search_text)){
        $search_text = '';
    }
    if(!isset($vintagelist)){
        $vintagelist = array();
    }
    if(!isset($product_id)){
        $product_id = null;
    }
    $autosearch = (strlen($search_text)||!empty($vintagelist))&&!$product_id;
?><!doctype html>
<html><head>
    <title>Wine Incognito</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="<?=BASE_URL?>/modules/Main/css/gallery.css" />
    <link rel="stylesheet" type="text/css" href="<?=BASE_URL?>/modules/Product/css/vintageoffersearcher.css" />
    <script src="<?=BASE_URL?>/modules/Main/js/jquery.min.js"></script>
    <script src="<?=BASE_URL?>/modules/Main/js/script.js"></script>
    <script src="<?=BASE_URL?>/modules/Main/js/gallery.js"></script>
    <script src="<?=BASE_URL?>/modules/Product/js/vintageoffersearcher.js"></script>
</head><body>
<table><tbody>
    <tr id="searchbar"><td class="searchbar" colspan="2"><form <?=$autosearch?'class="auto-search"':''?> <?=$product_id?'data-id="'.$product_id.'"':''?>><input type="text" value="<?=htmlentities($search_text)?>" /></form></td><td class="language-selector"><?php 
    if(!empty($language_selector)):
        foreach($language_selector as $language): 
            $language['name'] = mb_substr($language['name'],0,3,'UTF-8');
            if(!$language['current']):
        ?><a href="<?=$language['url']?>" data-code="<?=$language['code']?>"><?=htmlentities($language['name'])?></a><?php
            else: 
            ?><span><?=htmlentities($language['name'])?></span><?php
            endif;
        endforeach;
    endif; ?></td></tr>
    <tr id="vintage-selector"><td colspan="3">
        <script type="template" class="vintage-selector-item-template"><li {if{redundant}}class="redundant"{endif{redundant}}><input type="checkbox" name="vintage" id="vintage-selector-{{vintage}}" value="{{vintage}}" {if{selected}}checked="checked"{endif{selected}} /><label for="vintage-selector-{{vintage}}">{{vintage}}</label></li></script>
        <ul><?php 
    if(!empty($vintagelist)):
        foreach($vintagelist as $vintage):
            ?><li><input type="checkbox" name="vintage" id="vintage-selector-<?=htmlentities($vintage)?>" value="<?=htmlentities($vintage)?>" checked="checked" autocomplete="off" /><label for="vintage-selector-<?=htmlentities($vintage)?>"><?=htmlentities($vintage)?></label></li><?php
        endforeach;
    endif;
        ?></ul>
    </td></tr>
    <tr>
        <td id="sidebar">
            <script type="template" class="datalist-item-template"><tr class="item" data-pid="{{pid}}"><td class="name">{{name}}</td></tr></script>
            <script type="template" class="info-table-image-template"><img src="{{url}}" /></script>
            <script type="template" class="info-table-attribute-template"><tr><td class="label"><label>{{label}}</label></td></tr><tr><td class="value"><ul>{{values}}</ul></td></tr></script>
            <script type="template" class="info-table-attribute-value-template"><li>{{value}} {if{part}}({{part}}%){endif{part}}</li></script>
            <script type="template" class="info-table-template"><table class="info" data-id="{{id}}"><tbody>{if{images}}<tr><td class="wi-gallery gallery">{{images}}</td></tr>{endif{images}}<tr><td class="header">{{fullname}}</td></tr><tr><td class="label"><label><?=langTranslate('product','product','ID', 'ID')?></label></td></tr><tr><td class="value"><ul><li>{{id}}</li></ul></td></tr>{{attributes}}</tbody></table></script>
            <table class="datalist"><tbody>
            <tr class="noentries"><td><?=langTranslate('Sorry, no matches found', 'Sorry, no matches found');?></td></tr>
            <tr class="loading"><td></td></tr>
            <tr class="loadmore"><td><span><?=langTranslate('Load more', 'Load more');?></span></td></tr>
            </tbody></table>
        </td><td colspan="2" id="content">
            <script type="template" class="content-pagination-item-template"><li class="page {if{current}}current{endif{current}}" data-page="{{page}}">{{caption}}</li></script>
            <script type="template" class="content-pagination-separator-template"><li class="separator">&hellip;</li></script>
            <script type="template" class="content-item-template"><tr class="item" data-id={{id}}><td class="seller">{{company_name}}</td><td class="name">{{name}}</td><td class="score">{{score}}</td><td class="volume">{{volume}}</td><td class="price">{{price}}</td><td class="price normalized-price">{{normalized_price}}</td><td><a href="{{url}}"><?=langTranslate('Shop','Shop')?></a></td></tr></script>
            <table class="hidden"><thead><tr class="pagination"><th colspan="7"><ul></ul></th></tr><tr><th class="seller can-order-by" data-order-field="company"><?=langTranslate('Seller','Seller')?><span class="order-direction"></span></th><th class="name can-order-by" data-order-field="name"><?=langTranslate('Product','Product')?><span class="order-direction"></span></th><th class="score can-order-by" data-order-field="score"><span class="header non-sticky-tooltip" data-tooltip="<?=langTranslate('WineIncognito Score','WineIncognito Score')?>"></span><span class="order-direction"></span></th><th class="volume can-order-by" data-order-field="volume"><?=langTranslate('Volume','Volume')?><span class="order-direction"></span></th><th class="price can-order-by" data-order-field="price"><?=langTranslate('Price','Price')?><span class="order-direction"></span></th><th class="price normalized-price can-order-by" data-order-field="normalized-price"><?=langTranslate('Price per l.','Price per l.')?><span class="order-direction"></span></th><th class="url"></th></tr></thead><tfoot><tr><th class="seller can-order-by" data-order-field="company"><?=langTranslate('Seller','Seller')?><span class="order-direction"></span></th><th class="name can-order-by" data-order-field="name"><?=langTranslate('Product','Product')?><span class="order-direction"></span></th><th class="score can-order-by" data-order-field="score"><span class="header non-sticky-tooltip" data-tooltip="<?=langTranslate('WineIncognito Score','WineIncognito Score')?>"></span><span class="order-direction"></span></th><th class="volume can-order-by" data-order-field="volume"><?=langTranslate('Volume','Volume')?><span class="order-direction"></span></th><th class="price can-order-by" data-order-field="price"><?=langTranslate('Price','Price')?><span class="order-direction"></span></th><th class="price normalized-price can-order-by" data-order-field="normalized-price"><?=langTranslate('Price per l.','Price per l.')?><span class="order-direction"></span></th><th class="url"></th></tr><tr class="pagination"><th colspan="7"><ul></ul></th></tr></tfoot><tbody><tr class="noentries"><td colspan="7"><?=langTranslate('Sorry, no matches found', 'Sorry, no matches found');?></td></tr><tr class="loading"><td colspan="7"></td></tr><tr class="errmsg"><td colspan="7"></td></tr></tbody></table>
        </td>
    </tr>
</tbody></table>
</body></html>
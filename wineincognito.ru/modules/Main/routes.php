<?php
$routes = array();
$routes['/'] = '/main/';
//user
$routes['/login'] = '/user/login';
$routes['/login/([a-f0-9]{32,})'] = '/user/directauth/$1';
$routes['/passwordrecovery'] = '/user/passwordrecovery';
$routes['/logout'] = '/user/logout';
$routes['/users'] = '/user/userfilter';
$routes['/user/(\d+)'] = '/user/view/$1';
$routes['/user/(\d+)/edit'] = '/user/edit/$1';
$routes['/user/(\d+)/change_password'] = '/user/change_password/$1';
$routes['/user/(\d+)/settings'] = '/user/user_settings/$1';
$routes['/company/ai([^/]+)/join'] = '/user/join_company/autojoin/$1';
$routes['/profile'] = '/user/view_self';
$routes['/profile/edit'] = '/user/edit_self';
$routes['/profile/change_password'] = '/user/change_password_self';
$routes['/profile/settings'] = '/user/user_settings_self';
$routes['/profile/password/reset/([a-f0-9]{32})'] = '/user/password_reset/$1';
$routes['/register'] = '/user/register_user';
$routes['/company/create'] = '/user/register_company';
$routes['/mycompany'] = '/user/view_company_self';
$routes['/mycompany/edit'] = '/user/edit_company_self';
$routes['/mycompany/list'] = '/user/company_user_list_self';
$routes['/mycompany/settings'] = '/user/company_settings_self';
$routes['/mycompany/requests'] = '/user/company_joinrequests_self';
$routes['/company/(\d+)'] = '/user/view_company/$1';
$routes['/company/(\d+)/edit'] = '/user/edit_company/$1';
$routes['/company/(\d+)/list'] = '/user/company_user_list/$1';
// $routes['/company/(\d+)/requestjoin'] = '/user/requestjoin_company/$1';
$routes['/company/(\d+)/requests'] = '/user/company_joinrequests/$1';
$routes['/company/ai([^/]+)/join'] = '/user/join_company/autojoin/$1';
$routes['/companies'] = '/user/companylist';
//product
$routes['/moderate/products/approve'] = '/product/vintagefilter/approve';
$routes['/moderate/product/attributes'] = '/product/attrgrouplist';
$routes['/moderate/product/attributes/add'] = '/product/attrgroupadd';
$routes['/moderate/product/attributes/(\d+)/edit'] = '/product/attrgroupedit/$1';
$routes['/moderate/product/attributes/(\d+)'] = '/product/attrlist/$1';
$routes['/moderate/product/attributes/(\d+)/add'] = '/product/attradd/$1';
$routes['/moderate/product/attributes/(\d+)/(\d+)/edit'] = '/product/attredit/$1/$2';
$routes['/moderate/product/attributes/(\d+)/(\d+)'] = '/product/attrvallist/$2';
$routes['/moderate/product/attributes/(\d+)/(\d+)/add'] = '/product/attrvaladd/$2';
$routes['/moderate/product/attributes/(\d+)/(\d+)/(\d+)/edit'] = '/product/attrvaledit/$2/$3';
$routes['/translation/vintage/filter'] = '/product/vintagefilter/translations';
$routes['/translation/vintage/(\d+)'] = '/product/vintagetranslations/$1';

$routes['/product/add'] = '/product/productadd';
// $routes['/product/(\d+)'] = '/product/productview/$1';
// $routes['/product/(\d+)/edit'] = '/product/productedit/$1';
$routes['/product/(\d+)/vintage/add'] = '/product/vintageadd/$1';
// $routes['/products'] = '/product/productlist';
// $routes['/vintages'] = '/product/vintagelist';
// $routes['/products'] = '/product/vintagelist';
$routes['/products'] = '/product/vintagefilter/blanks';
$routes['/products/rated'] = '/product/vintagefilter/onlyscored';
$routes['/products/compare'] = '/product/compareproduct';
$routes['/products/compare/(\d+)'] = '/product/compareproduct/$1';
$routes['/products/compare/(\d+)/(\d+)'] = '/product/compareproduct/$1/$2';
$routes['/product/(\d+)/resolve'] = '/product/resolve_vintage_doubles_for_product/$1';//resolve vintage doubles after merging
$routes['/vintage/(\d+)'] = '/product/vintageview/$1';
$routes['/vintage/(\d+)/edit'] = '/product/vintageedit/$1';
$routes['/vintage/(\d+)/review/set'] = '/product/vintage_setpersonaltastingproduct/$1';
// $routes['/myreview/pending/tasting/(\d+)/filter'] = '/product/vintagefilter/pending_reviews_for_tasting/$1';
$routes['/myreview/pending/tasting/(\d+)/product/(\d+)'] = '/product/vintagereviewadd/$2/$1';
$routes['/myreview/pending/tasting/(\d+)/ranking'] = '/tasting/tastingrankingedit/$1';
$routes['/myreview/pending/product/(\d+)'] = '/product/vintagepersonalreviewadd/$1';
// $routes['/myreview'] = '/product/reviewlists_self';
$routes['/myreview/product/filter'] = '/product/vintagefilter/myreviews';
$routes['/myreview/tasting/filter'] = '/tasting/tastingfilter_myreviews';
$routes['/myreview/product/(\d+)/tasting/filter'] = '/tasting/tastingfilter_myreviews/$1';
$routes['/myreview/product/(\d+)/tasting/(\d+)/stats'] = '/tasting/myreview_tastingstatisticsview/$2/$1';
$routes['/myreview/tasting/(\d+)/stats'] = '/tasting/myreview_tastingstatisticsview/$1';
$routes['/review/(\d+)'] = '/product/vintagereviewview/$1';

$routes['/searcher'] = '/product/vintage_offer_searcher';
$routes['/searcher/s/([^/]+)'] = '/product/vintage_offer_searcher/$1';
$routes['/searcher/s/([^/]+)/v/([^/]+)'] = '/product/vintage_offer_searcher/$1/$2';
$routes['/searcher/v/([^/]+)'] = '/product/vintage_offer_searcher/0/$1';
// $routes['/searcher/s/([^/]+)/p/(\d+)'] = '/product/vintage_offer_searcher/$1/0/$2';
// $routes['/searcher/s/([^/]+)/v/([^/]+)/p/(\d+)'] = '/product/vintage_offer_searcher/$1/$2/$3';
$routes['/searcher/v/([^/]+)/p/(\d+)'] = '/product/vintage_offer_searcher/0/$1/$2';
$routes['/searcher/p/([^/]+)'] = '/product/vintage_offer_searcher/0/0/$1';
$routes['/ajax/searcher/search'] = '/product/ajax_vintage_offer_search';
$routes['/ajax/searcher/info/(\d+)'] = '/product/ajax_vintage_info/$1';
$routes['/ajax/searcher/pricelist'] = '/product/ajax_vintage_offer_pricelist_get';




// $routes['/myreview/vintage/(\d+)'] = '/product/vintageview/$1/myreviews';
$routes['/vintage/(\d+)/review/(\d+)'] = '/product/vintagereviewview/$2';
$routes['/vintage/(\d+)/review/(\d+)/edit'] = '/product/vintagereviewedit/$2';
$routes['/vintage/(\d+)/reviewmerge/(\d+)'] = '/product/vintagereviewmerge/$1/$2';
$routes['/vintage/(\d+)/reviewmerge/(\d+)/tasting/(\d+)'] = '/product/vintagereviewmerge/$1/$2/$3';
$routes['/vintage/(\d+)/reviewmerge/(\d+)/user/(\d+)'] = '/product/vintagereviewmerge/$1/$2/0/$3';
$routes['/vintage/(\d+)/reviewmerge/(\d+)/tasting/(\d+)/user/(\d+)'] = '/product/vintagereviewmerge/$1/$2/$3/$4';
$routes['/vintage/(\d+)/reviewmerge/personal'] = '/product/vintagereviewmerge_personal/$1';

$routes['/vintage/(\d+)/scoredetails'] = '/product/vintagescoredetails/$1';
$routes['/vintage/(\d+)/scoredetails/user/(\d+)'] = '/product/vintagescoredetails/$1/$2';
$routes['/vintage/(\d+)/scoredetails/tasting/(\d+)'] = '/product/vintagescoredetails/$1/0/$2';

//lang
$routes['/translation/interface/modules'] = '/lang/modules';
$routes['/translation/interface/module/(\d+)/groups'] = '/lang/groups/$1';
$routes['/translation/interface/module/(\d+)/group/(\d+)'] = '/lang/editinterfacetranslations/$1/$2';





//tasting
$routes['/tastings'] = '/tasting/tastingfilter';
$routes['/tasting/add'] = '/tasting/tastingadd';
$routes['/tasting/(\d+)'] = '/tasting/tastingview/$1';
$routes['/tasting/(\d+)/edit'] = '/tasting/tastingedit/$1';
// $routes['/tasting/(\d+)/respond/(\d+)'] = '/tasting/tasting_user_respond/$1/$2';
$routes['/tasting/(\d+)/user/(\d+)/code/(\d+)/respond/(\d+)'] = '/tasting/external_tasting_user_respond/$1/$2/$3/$4';

$routes['/tasting/(\d+)/stats'] = '/tasting/tastingstatisticsview/$1';
$routes['/tasting/(\d+)/stats/product/(\d+)'] = '/tasting/tastingstatisticsview/$1/$2';
$routes['/tasting/(\d+)/stats/product/(\d+)/review/(\d+)'] = '/product/vintagereviewview/$3';
$routes['/tasting/(\d+)/stats/product/(\d+)/reviewmerge/(\d+)'] = '/product/vintagereviewmerge_ongoing_from_tpv_id/$2/$3';
$routes['/tasting/(\d+)/stats/user/(\d+)'] = '/tasting/tastingstatisticsview/$1/0/$2';
$routes['/tasting/(\d+)/product/(\d+)/evaluation/manual/set'] = '/tasting/tastingsetmanualevaluation_for_tasting_and_tastingproduct/$1/$2';
$routes['/tasting/(\d+)/product/(\d+)/evaluation/manual/view'] = '/tasting/tastingviewmanualevaluation_for_tasting_and_tastingproduct/$1/$2';
// $routes['/tasting/(\d+)/product/(\d+)/certificate'] = '/tasting/tastingproduct_certificate/$1/$2';
$routes['/tasting/(\d+)/swapreviews'] = '/tasting/tastingswapreviews_for_tasting/$1';
$routes['/tasting/(\d+)/swapreviews/user/(\d+)'] = '/tasting/tastingswapreviews_for_tasting_and_user/$1/$2';

$routes['/moderate/tastings/approve'] = '/tasting/tastingfilter/approve';

$routes['/moderate/tasting/expert_evaluation_template'] = '/tasting/global_expert_evaluation_template';


//contest
$routes['/contests'] = '/tasting/contestfilter';
$routes['/contest/add'] = '/tasting/contestadd';
$routes['/contest/(\d+)'] = '/tasting/contestview/$1';
$routes['/contest/(\d+)/edit'] = '/tasting/contestedit/$1';
$routes['/contest/(\d+)/stats'] = '/tasting/conteststatisticsview/$1';
$routes['/contest/(\d+)/stats/product/(\d+)'] = '/tasting/conteststatisticsview/$1/$2';
$routes['/contest/(\d+)/stats/product/(\d+)/reviewmerge/(\d+)'] = '/product/vintagereviewmerge/$2/$3/0/0/$1';
$routes['/contest/(\d+)/stats/product/(\d+)/user/(\d+)/reviewmerge/(\d+)'] = '/product/vintagereviewmerge/$2/$4/0/$3/$1';
$routes['/contest/(\d+)/stats/product/(\d+)/reviewmerge/personal'] = '/product/vintagereviewmerge_personal/$2/$1';
$routes['/contest/(\d+)/stats/user/(\d+)'] = '/tasting/conteststatisticsview/$1/0/$2';
$routes['/contest/(\d+)/product/(\d+)/certificate'] = '/tasting/product_certificate/$2/$1';

//api
$routes['/api'] = '/api/api';

//static
$routes['/about/privacy-policy'] = '/page/view/privacy-policy';

//moderate
$routes['/moderate/contests/approve'] = '/tasting/contestfilter/approve';




$routes['/myreview/pending/tasting/filter'] = '/tasting/pendingreviewtastingfilter';
$routes['/myreview/pending/tasting/(\d+)/products'] = '/tasting/pendingtastingproducts/$1';

//main
$routes['/ajax/keepalive'] = '/main/ajax_keepalive';
//user
$routes['/ajax/user/expert/request'] = '/user/ajax_requestexpert';
$routes['/ajax/user/(\d+)/expertrequest/(\d+)/resolve'] = '/user/ajax_resolve_expertrequest/$2';
$routes['/ajax/user/(\d+)/expert_level/set'] = '/user/ajax_setexpertlevel_user/$1';
$routes['/ajax/user/(\d+)/access/change'] = '/user/ajax_changeaddright_user/$1';
$routes['/ajax/user/(\d+)/directauth/set'] = '/user/ajax_setdirectauth_user/$1';
$routes['/ajax/user/favourite'] = '/user/ajax_favourite_user';
$routes['/ajax/company/(\d+)/resolvejoinrequest/(\d+)'] = '/user/ajax_resolvejoinrequest/$1/$2';
$routes['/ajax/company/(\d+)/dismissuser/(\d+)'] = '/user/ajax_dismissuser/$1/$2';
$routes['/ajax/company/(\d+)/requestjoin'] = '/user/ajax_requestjoin_company/$1';
$routes['/ajax/company/(\d+)/delete'] = '/user/ajax_delete_company/$1';
$routes['/ajax/company/(\d+)/apiaccess/grant'] = '/user/ajax_company_change_api_access/$1/1';
$routes['/ajax/company/(\d+)/apiaccess/revoke'] = '/user/ajax_company_change_api_access/$1/0';
$routes['/ajax/company/(\d+)/approve'] = '/user/ajax_approve_company/$1';
$routes['/ajax/user/search'] = '/user/ajax_search_user';
$routes['/ajax/user/filter/form'] = '/user/ajax_get_user_filter_form';
$routes['/ajax/company/(\d+)/mailsettings/image/upload'] = '/user/ajax_company_mail_settings_upload_image/$1';

//product
$routes['/ajax/moderate/product/attributes/(\d+)/hide'] = '/product/ajax_hide_attrgroup/$1';
$routes['/ajax/moderate/product/subattributes/(\d+)/hide'] = '/product/ajax_hide_attr/$1';
$routes['/ajax/attributes/childvalues'] = '/product/ajax_get_attr_val_tree';
$routes['/ajax/attributes/(\d+)/form'] = '/product/ajax_get_attr_val_form/$1';
$routes['/ajax/attributes/(\d+)/add'] = '/product/ajax_add_attr_val/$1';
$routes['/ajax/product/fullnametemplates'] = '/product/ajax_get_full_name_templates';
$routes['/ajax/product/image/upload'] = '/product/ajax_upload_images';
$routes['/ajax/product/image/delete'] = '/product/ajax_delete_image';
$routes['/ajax/product/image/make_primary'] = '/product/ajax_make_image_primary';
$routes['/ajax/product/check'] = '/product/ajax_check_for_doubles';
$routes['/ajax/vintage/search'] = '/product/ajax_search_vintage';
$routes['/ajax/vintage/favourite'] = '/product/ajax_favourite_vintage';
$routes['/ajax/product/company_favourite'] = '/product/ajax_company_favourite_product';
$routes['/ajax/product/filter/form'] = '/product/ajax_get_product_filter_form';
$routes['/ajax/product/(\d+)/vintage/check'] = '/product/ajax_check_vintage_for_doubles/$1';
$routes['/ajax/product/(\d+)/vintage/add/form'] = '/product/ajax_vintageadd_form/$1';
$routes['/ajax/product/(\d+)/vintage/add'] = '/product/ajax_vintageadd/$1';
$routes['/ajax/product/(\d+)/approve'] = '/product/ajax_productapprove/$1';
$routes['/ajax/product/merge/(\d+)/into/(\d+)'] = '/product/ajax_productmerge/$2/$1';
$routes['/ajax/vintage/merge/(\d+)/into/(\d+)'] = '/product/ajax_vintagemerge/$2/$1';
// $routes['/ajax/product/(\d+)/delete'] = '/product/ajax_productdelete/$1';
$routes['/ajax/vintage/(\d+)/delete'] = '/product/ajax_vintagedelete/$1';
$routes['/ajax/vintage/(\d+)/view/form'] = '/product/ajax_vintageview_form/$1';
$routes['/ajax/translation/vintage/(\d+)/approve'] = '/product/ajax_vintage_translation_approve/$1';
$routes['/ajax/moderate/product/attributes/(\d+)/alternatespelling/add'] = '/product/ajax_add_attribute_alternate_spelling/$1';
$routes['/ajax/moderate/product/attributes/(\d+)/alternatespelling/(\d+)/edit'] = '/product/ajax_edit_attribute_alternate_spelling/$2';
$routes['/ajax/moderate/product/attributes/(\d+)/alternatespelling/(\d+)/remove'] = '/product/ajax_remove_attribute_alternate_spelling/$2';
$routes['/ajax/moderate/product/attributes/(\d+)/analog/list'] = '/product/ajax_attribute_analog_list/$1';
$routes['/ajax/moderate/product/attributes/(\d+)/analog/add'] = '/product/ajax_add_attribute_analog/$1';
$routes['/ajax/moderate/product/attributes/(\d+)/analog/(\d+)/remove'] = '/product/ajax_remove_attribute_analog/$1/$2';

//tasting
$routes['/ajax/tasting/(\d+)/product/(\d+)/edit/form'] = '/tasting/ajax_edit_tasting_product_vintage_form/$2';
$routes['/ajax/tasting/(\d+)/product/(\d+)/edit'] = '/tasting/ajax_edit_tasting_product_vintage/$2';
$routes['/ajax/tasting/(\d+)/product/(\d+)/modifyindex'] = '/tasting/ajax_modifyindex_tasting_product_vintage/$2';
$routes['/ajax/tasting/(\d+)/product/(\d+)/reviews/request'] = '/tasting/ajax_change_review_status_tasting_product_vintage/$2/1';
$routes['/ajax/tasting/(\d+)/product/(\d+)/reviews/stop'] = '/tasting/ajax_change_review_status_tasting_product_vintage/$2/2';
$routes['/ajax/tasting/(\d+)/product/(\d+)/remove'] = '/tasting/ajax_remove_tasting_product_vintage/$2';
$routes['/ajax/tasting/(\d+)/product/add'] = '/tasting/ajax_add_tasting_product_vintage/$1';
$routes['/ajax/tasting/(\d+)/product/list'] = '/product/ajax_get_tasting_vintage_list/$1';
$routes['/ajax/tasting/(\d+)/user/(\d+)/remove'] = '/tasting/ajax_remove_tasting_user/$2/$1';
$routes['/ajax/tasting/(\d+)/user/(\d+)/mark_presence'] = '/tasting/ajax_tasting_user_mark_presence/$2/$1';
$routes['/ajax/tasting/(\d+)/user/invite'] = '/tasting/ajax_invite_tasting_user/$1';
$routes['/ajax/tasting/(\d+)/user/list'] = '/tasting/ajax_get_tasting_user_list/$1';
$routes['/ajax/tasting/(\d+)/user/respond'] = '/tasting/ajax_tasting_user_respond/$1';
$routes['/ajax/tasting/(\d+)/status/change'] = '/tasting/ajax_tasting_status_change/$1';
$routes['/ajax/tasting/search'] = '/tasting/ajax_search_tasting';
$routes['/ajax/tasting/(\d+)/vintage/(\d+)/preparation/form'] = '/tasting/ajax_get_vintage_preparation_form/$2';
$routes['/ajax/tasting/(\d+)/vintage/(\d+)/preparation/change'] = '/tasting/ajax_vintage_preparation_change/$2';
$routes['/ajax/tasting/(\d+)/evaluation/base/change'] = '/tasting/ajax_tasting_evaluation_change/$1';
$routes['/ajax/tasting/expert_evaluation/base/change'] = '/tasting/ajax_global_expert_evaluation_template_set';
$routes['/ajax/tasting/expert_evaluation/base/refresh'] = '/tasting/ajax_global_expert_evaluation_refresh';
$routes['/ajax/tasting/(\d+)/particularity/change'] = '/tasting/ajax_tasting_particularity_change/$1';
$routes['/ajax/tasting/(\d+)/assess/approve'] = '/tasting/ajax_tasting_assess/$1/1';
$routes['/ajax/tasting/(\d+)/assess/deny'] = '/tasting/ajax_tasting_assess/$1/0';
$routes['/ajax/tasting/(\d+)/product/(\d+)/user/(\d+)/blockreview'] = '/tasting/ajax_block_review_tasting_product_user/$2/$3';
$routes['/ajax/tasting/(\d+)/swapreviews/user/(\d+)/swap'] = '/tasting/ajax_tastingswapreviews_swap/$1/$2';


//contest
$routes['/ajax/contest/search'] = '/tasting/ajax_search_contest';
$routes['/ajax/contest/(\d+)/user/list'] = '/tasting/ajax_get_contest_user_access_list/$1';
$routes['/ajax/contest/(\d+)/user/invite'] = '/tasting/ajax_grant_contest_user_access/$1';
$routes['/ajax/contest/(\d+)/user/(\d+)/remove'] = '/tasting/ajax_revoke_contest_user_access/$1/$2';
$routes['/ajax/contest/(\d+)/tasting/list'] = '/tasting/ajax_get_contest_tasting_list/$1';
$routes['/ajax/tasting/filter/form'] = '/tasting/ajax_get_tasting_filter_form';
$routes['/ajax/contest/(\d+)/tasting/add'] = '/tasting/ajax_add_contest_tasting/$1';
$routes['/ajax/contest/(\d+)/tasting/(\d+)/remove'] = '/tasting/ajax_remove_contest_tasting/$1/$2';
$routes['/ajax/contest/(\d+)/delete'] = '/tasting/ajax_contest_delete/$1';
$routes['/ajax/contest/(\d+)/status/change'] = '/tasting/ajax_contest_status_change/$1';
$routes['/ajax/contest/(\d+)/nomination/add/form'] = '/tasting/ajax_contest_nomination_form';
$routes['/ajax/contest/(\d+)/nomination/(\d+)/edit/form'] = '/tasting/ajax_contest_nomination_form/$2';
$routes['/ajax/contest/(\d+)/nomination/add'] = '/tasting/ajax_add_contest_nomination/$1';
$routes['/ajax/contest/(\d+)/nomination/(\d+)/edit'] = '/tasting/ajax_edit_contest_nomination/$2';
$routes['/ajax/contest/(\d+)/nomination/(\d+)/remove'] = '/tasting/ajax_remove_contest_nomination/$2';
$routes['/ajax/contest/(\d+)/nomination/(\d+)/modifyindex'] = '/tasting/ajax_modifyindex_contest_vintage/$2';
$routes['/ajax/contest/(\d+)/nomination/list'] = '/tasting/ajax_get_contest_nomination_list/$1';
$routes['/ajax/contest/(\d+)/nomination/(\d+)/winner/add/form'] = '/tasting/ajax_contest_nomination_winner_form/$2';
$routes['/ajax/contest/(\d+)/nomination/(\d+)/winner/add'] = '/tasting/ajax_add_contest_nomination_winner/$2';
$routes['/ajax/contest/(\d+)/nomination/(\d+)/winner/(\d+)/remove'] = '/tasting/ajax_remove_contest_nomination_winner/$2/$3';
$routes['/ajax/contest/(\d+)/assess/approve'] = '/tasting/ajax_contest_assess/$1/1';
$routes['/ajax/contest/(\d+)/assess/deny'] = '/tasting/ajax_contest_assess/$1/0';
//lang
$routes['/ajax/translation/interface/item/(\d+)/edit'] = '/lang/ajax_interfacetranslation_edit/$1';


$routes['/moderate/companies/approve'] = '/user/approve_company_list';
$routes['/moderate/user/experts/approve'] = '/user/userfilter/approve_experts';
$routes['/moderate/user/experts/rating'] = '/user/userfilter/global_expert_scores';
$routes['/moderate/user/experts/rating/(\d+)'] = '/tasting/tastingfilter/global_evaluation_for_user/$1';
$routes['/moderate/user/(\d+)/expert/approve'] = '/user/expertrequests_user/$1';
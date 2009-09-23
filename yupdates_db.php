<?php

define("YUPDATES_USER_OPTION", "yupdates_updates_widget_users");
define("YUPDATES_CONSUMER_KEY_OPTION", "yupdates_consumer_key");
define("YUPDATES_CONSUMER_SECRET_OPTION", "yupdates_consumer_secret");
define("YUPDATES_APPLICATION_ID_OPTION", "yupdates_application_id");
define("YUPDATES_WIDGET_COUNT_OPTION", "yupdates_widget_count");
define("YUPDATES_TITLE_TEMPLATE_OPTION", "yupdates_title_template");

$updateUsers = get_option(YUPDATES_USER_OPTION);
if(is_bool($updateUsers) && !$updateUsers) {
    $updateUsers = array();
}
else if(!is_array($updateUsers)) {
    $updateUsers = array();
    delete_option(YUPDATES_USER_OPTION);
}

function yupdatesdb_hasApplicationInfo() {
    return get_option(YUPDATES_CONSUMER_KEY_OPTION) &&
            get_option(YUPDATES_CONSUMER_SECRET_OPTION) &&
            get_option(YUPDATES_APPLICATION_ID_OPTION);
}

function yupdatesdb_getApplicationInfo() {
    $info = array();
    $info["ck"] = get_option(YUPDATES_CONSUMER_KEY_OPTION);
    $info["cks"] = get_option(YUPDATES_CONSUMER_SECRET_OPTION);
    $info["appid"] = get_option(YUPDATES_APPLICATION_ID_OPTION);
    return $info;
}

function yupdatesdb_addUpdatesUser($user) {
    global $updateUsers;
    $updateUsers[$user] = true;
    update_option(YUPDATES_USER_OPTION, $updateUsers);
}

function yupdatesdb_removeUpdatesUser($user) {
    global $updateUsers;
    $updateUsers[$user] = false;
    update_option(YUPDATES_USER_OPTION, $updateUsers);
}

function yupdatesdb_isUpdatesUser($user) {
    global $updateUsers;
    return array_key_exists($user, $updateUsers) && $updateUsers[$user];
}

function yupdatesdb_listUpdatesUsers() {
    global $updateUsers;

    $users = array();
    foreach($updateUsers as $user => $active) {
        if($active) {
            $users[] = $user;
        }
    }

    return $users;
}

function yupdatesdb_getWidgetCount() {
    $count = get_option(YUPDATES_WIDGET_COUNT_OPTION);
    if(is_bool($count) || !is_numeric($count)) {
        $count = 5;
    }
    return $count;
}

function yupdatesdb_setWidgetCount($count) {
    update_option(YUPDATES_WIDGET_COUNT_OPTION, $count);
}

?>

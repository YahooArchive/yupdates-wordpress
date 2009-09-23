<?php
/**
 * Yahoo! Updates Wordpress Plugin
 *
 * Find documentation and support on Yahoo! Developer Network: http://developer.yahoo.com
 *
 * Hosted on GitHub: http://github.com/yahoo/yos-updates-wordpress/tree/master
 *
 * @package    yos-updates-wordpress
 * @subpackage yahoo
 *
 * @author     Ryan Kennedy <rckenned@yahoo-inc.com>, Lawrence Morrisroe <lem@yahoo-inc.com>, Zach Graves <zachg@yahoo-inc.com>
 * @copyright  Copyrights for code authored by Yahoo! Inc. is licensed under the following terms:
 * @license    BSD Open Source License
 *
 *   Permission is hereby granted, free of charge, to any person obtaining a copy
 *   of this software and associated documentation files (the "Software"), to deal
 *   in the Software without restriction, including without limitation the rights
 *   to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 *   copies of the Software, and to permit persons to whom the Software is
 *   furnished to do so, subject to the following conditions:
 *
 *   The above copyright notice and this permission notice shall be included in
 *   all copies or substantial portions of the Software.
 *
 *   THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 *   IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 *   FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 *   AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 *   LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 *   OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 *   THE SOFTWARE.
 **/

function yupdates_yahoo_updates_widget($args) {
    extract($args);

    $ck = get_option("yupdates_consumer_key");
    $cks = get_option("yupdates_consumer_secret");
    $appid = get_option("yupdates_application_id");

    $users = yupdatesdb_listUpdatesUsers();
    $updates = array();
    foreach($users as $user) {
        YahooSession::setSessionStore(new WordPressSessionStore($user));
        if(YahooSession::hasSession($ck, $cks, $appid)) {
            $session = YahooSession::requireSession($ck, $cks, $appid);
            $user = $session->getSessionedUser();
            $userUpdates = $user->listUpdates(0, 20);
            if(!is_null($userUpdates)) {
                $updates = array_merge($updates, $userUpdates);
            }
        }
    }

    usort($updates, "yupdates_compare_updates");

    echo $before_widget;
    echo $before_title . "Yahoo! Updates" . $after_title;
    $includedUpdates = 0;
    $widgetCount = yupdatesdb_getWidgetCount();
    for($i = 0; $i < count($updates) && $includedUpdates < $widgetCount; $i++) {
        $update = $updates[$i];
        if(!property_exists($update, "SCL") || $update->SCL === "PUBLIC") {
            echo sprintf("<div><div style='display: block; float: left; clear: none'><img src='%s' style='width: 16px'></div><div style='margin: 0px 0px 0px 22px'><a target='_new' href='%s'>%s</a><br>%s</div></div>", 
                    $update->loc_iconURL, $update->link, $update->loc_longForm, 
                    yupdates_ago($update->lastUpdated));
            if($i + 1 < count($updates)) {
                echo "<hr>";
            }
            $includedUpdates++;
        }
    }
    if($includedUpdates == 0) {
        echo "No updates at this time";
    }
    echo $after_widget;
}

function yupdates_yahoo_updates_widget_control() {
    $count = yupdatesdb_getWidgetCount();
    if($_POST["yupdateswidgetsubmit"]) {
        $newCount = $_POST["yupdateswidgetcount"];
        if(is_numeric($newCount) && ($newCount >= 0)) {
            yupdatesdb_setWidgetCount($newCount);
            $count = $newCount;
        }
    }
?>
    <p><label for="yupdates-widget-count">Number of Updates to Display: <input type="text" id="yupdates-widget-count" name="yupdateswidgetcount" size="2" value="<?php echo $count; ?>"></label></p>
    <input type="hidden" name="yupdateswidgetsubmit" value="1">
<?php
}

if(YUPDATES_WIDGET_ENABLED) {
    add_action("init", "yupdates_register_widgets");
}

function yupdates_register_widgets() {
    register_sidebar_widget("Yahoo! Updates", "yupdates_yahoo_updates_widget", null, "yupdates");
    register_widget_control("Yahoo! Updates", "yupdates_yahoo_updates_widget_control", null, 75, "yupdates");
}

function yupdates_compare_updates($a, $b) {
    return $b->lastUpdated - $a->lastUpdated;
}

function yupdates_ago($timestamp) {
    $difference = time() - $timestamp;
    $unit = NULL;

    if($difference < 60) {
        return "moments ago";
    }
    else {
        $difference = round($difference / 60);
        if($difference < 60) {
            $unit = $difference == 1 ? "minute" : "minutes";
        }
        else {
            $difference = round($difference / 60);
            if($difference < 24) {
                $unit = $difference == 1 ? "hour" : "hours";
            }
            else {
                $difference = round($difference / 24);
                if($difference < 7) {
                    $unit = $difference == 1 ? "day" : "days";
                }
                else {
                    return "a while ago";
                }
            }
        }
    }

    return sprintf("%d %s ago", $difference, $unit);
}

?>

@extends('layouts_app')
@section('content')
    <STYLE>
        li > .title {
            font-weight: bold;
        }

        .collapse, .collapsing {
            margin-left: 30px;
        }

        .btn:not(.btn-circle) {
        }

        .btn-wide{
            width: 150px !important;
        }

        jump, .jump {
            text-decoration: underline;
            cursor: pointer;
            color: blue;
        }

        jump.event{
            text-decoration: none !important;
            font-weight: bold;
            color: black !important;
        }

        .no-u{
            text-decoration: none !important;
        }

        #gotobottom{
            bottom: 0;
        }
        #expandall{
            bottom: 28px;
        }
        #contractall{
            bottom: 56px;
        }
        #gototop {
            bottom: 84px;
        }

        .footer{
            font-weight: bold;
            position: fixed;
            left: 0;
            display: table;
            margin: 0 auto;
            background-color: white;
            width: 150px !important;
            color: #0281E1;
            z-index: 999;
            border: 1px solid #0281E1 !important;
            text-align: left !important;
        }

        .bg-secondary {
            margin-bottom: 2px;
        }

        .fa-black {
            border-radius: 3px;
            background-color: #292B2C;
            color: white;
            width: 20px;
            height: 20px;
            text-align: center;
            padding-top: 1px;
        }

        .fa-black.fa-plus{
            padding-top: 2px;
            padding-left: 1px;
        }

        .reason{
            font-weight: bold;
            color: blue;
        }

        .tab{
            margin-left: 25px;
        }

        .btn-border{
            border: 1px solid black !important;
        }
    </STYLE>
    <SCRIPT>
        $(document).ready(function () {
            $("jump").click(function () {
                var target = "#item_" + toclassname($(this).text());
                var targetstarget = $(target).attr("data-target");
                if (!$(targetstarget).hasClass("show")) {
                    $(target).trigger("click");
                    scrollto($(target).offset().top);
                }
            });
            $("#profileinfo").remove();
            //$(".sticky-footer").remove();

            $('a[href=#top]').click(function(event){
                event.preventDefault();
                scrollto(0);
            });
            $('a[href=#bottom]').click(function(event){
                event.preventDefault();
                scrollto($(document).height());
            });
        });

        function scrollto(Y){
            $('html, body').animate({scrollTop: Y}, 'slow');
        }

        function expandall(expand){
            if(expand){
                $(".collapse").not(".show").prev().trigger("click");
            } else {
                $(".show").prev().trigger("click");
            }
        }
    </SCRIPT>
    <?php
        $site_name = sitename;
        $launchdate = "April 1, 2017";
        $datestamp = strtotime($launchdate);
        $SQLdate = date("Y-m-d", $datestamp);
        $launched = iif(time() > $datestamp, " (Launched)");
        if(!$launched){
            $days = ceil(($datestamp - time()) / 86400) ;
            $launched = " (" . $days . " day" . iif($days > 1, "s") . " away)";
        }
        $orders =  first('SELECT count(*) as count FROM orders WHERE status <> 2 AND status <> 4 AND placed_at > "' . $SQLdate . '"')["count"];
    ?>
    <DIV class="row py-3 bg-white">
        <div class="col-sm-6 list-padding">
            <h3>Our Duty </h3>

            <div class="card-block bg-danger text-white">
                <p>
                    <?= $site_name; ?> is a pizza delivery service that's "faster than picking up the phone".

                    Created by Van and Roy of Hamilton; we've seen what's out there for online ordering and we're confident that we can do better.
<br><br>


                        We believe in the community and we must give back at all cost.
                    That's why we're pledging to donate $0.25 from every order the local food bank. With your support; we will help many people for many years. This is the lifetime commitment of <?= $site_name; ?>.
<br><br>

                        Thank you for your support.

                </p>
                <hr>

                <div class="btn-outlined-danger text-center pt-1">
                    <strong>June, 2017</strong>
                    <p> Orders: <?= $orders; ?>
                    <br> Donated: $<?= number_format((float)$orders * 0.25, 2, '.', ''); ?>
                    <br> Charity: London Food Bank</p>
                </div>
            </div>
        </div>
        <div class="col-sm-6 list-padding">


        <h3>FAQ</h3>
    <?php
        $email = '<A HREF="mailto:info@trinoweb.ca?subject=' . $site_name . '">info@trinoweb.ca</A>';
        $minimum = first("SELECT price FROM additional_toppings WHERE size = 'Minimum'")["price"];

        function toclass($text) {
            $text = str_replace('/', '_', $text);
            $text = strtolower(str_replace(" ", "_", trim(strip_tags($text))));
            return $text;
        }
        function newID() {
            if (isset($GLOBALS["lastid"])) {
                $GLOBALS["lastid"] += 1;
            } else {
                $GLOBALS["lastid"] = 0;
            }
            return lastID();
        }
        function lastID() {
            return "section_" . $GLOBALS["lastid"];
        }
        function newlist($Title) {
            if (isset($GLOBALS["startlist"])) {
                echo '</UL>';
            }
            $GLOBALS["startlist"] = true;
            echo '<H2>' . $Title . '</H2><UL>';
        }
        function newitem($Title, $Text, $Class = "") {
            echo '<LI data-toggle="collapse" data-target="#' . newID() . '" ID="item_' . toclass($Title) . '">';
            echo '<SPAN CLASS="title cursor-pointer ' . $Class . '">' . $Title . '</SPAN></LI>';
            echo '<div id="' . lastID() . '" class="collapse">' . $Text . '</div>';
        }
        function actionitem($action, $text = '') {
            $actions = actions($action);
            $parties = ["User", "Admin", "Restaurant"];
            $tempstr = "";
            if(!count($actions)){
                $tempstr = "<BR>No actions are assigned to this event";
            }
            foreach ($actions as $actiond) {
                $tempstr2 = "The " . $parties[$actiond["party"]] . " is: ";
                $actione = array();
                if ($actiond["sms"]) {
                    $actione[] = "texted";
                }
                if ($actiond["phone"]) {
                    $actione[] = "called";
                }
                if ($actiond["email"]) {
                    $actione[] = "emailed";
                }
                $tempstr2 .= join("/", $actione) . ' with the message/subject "' . str_replace("[reason]", '<span class="reason">[reason]</span>', $actiond["message"]) . '"';
                $tempstr .= '<BR>' . $tempstr2;
            }
            newitem($action, 'Occurs when: ' . $text . $tempstr);
        }

        $GLOBALS["currentnumber"] = 1;
        function p($isletter = false){
            if($isletter){
                echo '<P CLASS="tab">' . $GLOBALS["currentletter"] . ") ";
                $GLOBALS["currentletter"] = chr(ord($GLOBALS["currentletter"]) + 1);
            } else {
                echo '<P>' . $GLOBALS["currentnumber"] . ". ";
                $GLOBALS["currentnumber"] += 1;
                $GLOBALS["currentletter"] = "a";
            }
        }

        newlist("Users");
        newitem("Signing in", "Enter your email address and password in the <A HREF='" . webroot("/") . "'>Log In</A> page and click <button class='btn btn-sm btn-primary'>LOG IN</button>");
        newitem("Forgot password", "Enter the email address you registered with, click <button class='btn btn-secondary btn-wide btn-sm'>Forgot Password</button> and a new password will be emailed to you");
        newitem("Registering", "Click the 'Signup' tab, enter a valid London address into the 'Delivery Address' field (use 'Address Notes' for things like apartment/unit/back door/etc), enter your name/email/password and click <Button class='btn btn-sm btn-primary'>Register</button>");
        newitem('<i class="fa fa-fw fa-bars"></i> button', "A dropdown menu with various options, located in the top-left corner");
        newitem('<i class="fa fa-fw fa-user"></i> <SPAN CLASS="session_name"></SPAN>', "A popup to edit your user name/phone number/password/credit card numbers/addresses");
        newitem('<i class="fa fa-fw fa-clock-o"></i> Past Orders', "A popup that shows a list of your previous orders. Clicking <button class='btn btn-sm btn-primary'>Load Order</button> will overwrite the contents of your cart with that order");
        newitem('<i class="fa fa-fw fa-home"></i> Log Out', "Logs you out and returns to the login/register page");

        newlist('How to order');
        newitem("Add an item to your cart", "Click the item on the menu. If it has a + next to the price, there will be a popup allowing you to edit the item options before adding it to the receipt");
        newitem("Topping/sauces popup", 'If the menu item contains more than 1 item (ie: 2 pizzas), there will be a list at the top of this popup to select which item to edit. Clicking any of the options from the list will add it to the selected item. Some options are part of a group and only 1 option in that group can be added to an item (ie: well done and lightly done will conflict, so only 1 can be added to a pizza). The price will update automatically when you add options.<BR><button class="btn btn-sm mt-0 toppings_btn bg-secondary flat-border"><i class="fa fa-check"></i><SPAN CLASS="pull-right">$X.XX</SPAN></button> will add the item with the options you selected to the receipt.<BR><button class="btn btn-sm bg-secondary toppings_btn"><i class="fa fa-fw fa-arrow-left"></i></button> will remove the last option added to the selected item, if it is not dimmed');
        newitem("Editing an item in your cart", 'Click <button class="fa fa-pencil btn-sm"></button> to the right of the item in the receipt, the same popup you used to add the item will appear');
        newitem("Remove an item from your cart", 'Click <button class="fa fa-minus btn-sm"></button> to the right of the item in the receipt');
        newitem("Duplicating an item in your cart", 'Click <button class="fa fa-plus btn-sm"></button> to the right of the item in the receipt (if it is a simple item without any addons/toppings)');
        newitem("Empty your cart", 'Click <i class="fa fa-close"></i> at the top-left corner of your receipt');
        newitem('<i class="fa fa-fw fa-shopping-basket"></i> CHECKOUT', "Click this when you're done placing your order. You'll need to enter your <jump>Payment Information</jump>, <jump>Delivery Address</jump>, <jump>Preferred Restaurant</jump>, <jump>Delivery Time</jump>, then click <BUTTON CLASS='btn btn-primary btn-sm'>Place order</BUTTON>.<BR>This button will only be visible once your order meets the minumum of: $" . $minimum . " before taxes and delivery", "btn btn-primary btn-sm btn-block btn-wide");
        newitem("Payment Information", "If you have a saved card (note: Cards are saved with Stripe, not our servers) you can select it from the dropdown, or use 'Add Card' to add a new one. Otherwise just enter your credit card information");
        newitem("Delivery Address", "If you have a saved address you can select it from the dropdown, or select 'Add Address' to add a new address. Otherwise just enter a valid London address");
        newitem("Preferred Restaurant", "Select which restaurant you want to recieve your order from");
        newitem("Delivery time", "Leave as 'Deliver Now' to have the store deliver it ASAP. Otherwise they'll try to deliver as close to your selected time as possible.");

        if (read("id") && read("profiletype") > 0) {
            newlist("Restaurants");
            newitem("Registering", "You can only register as a regular user. To get escalated to a restaurant account requires you to contact an admin at: " . $email);
            newitem('<i class="fa fa-fw fa-user-plus"></i> Orders List', "Shows a list of orders for your restaurant");
            newitem("View", "View the contents of the order, a map showing the customer's address, and gives the options to Confirm, Email and Decline the order", "btn btn-sm btn-success btn-border btn-wide");
            newitem("Delete", "Trigger the <jump class='event'>order_declined</jump> event and delete the order from the system", "btn btn-sm btn-border btn-wide");
            newitem("Confirmed", "Mark the order as confirmed and trigger the <jump class='event'>order_confirmed</jump> event", "btn btn-sm btn-primary btn-border btn-wide");
            newitem('<i class="fa fa-fw fa-envelope"></i> Email', "Re-send the receipt to customer via the <jump class='event'>order_placed</jump> event", "btn btn-sm btn-secondary red btn-border btn-wide");
            newitem("Declined", 'Mark the order as declined and trigger the <jump class="event">order_declined</jump> event', "btn btn-sm btn-border btn-wide");
            newitem("Delivered", 'Mark the order as delivered and trigger the <jump class="event">order_delivered</jump> event', "btn btn-sm btn-warning btn-border btn-wide");
            newitem("FILE NOT FOUND", "The order file is missing. Delete the order as the order itself is useless");

            newlist("Communication Actions");
            newitem("Editing actions", 'This can only done in <B><i class="fa fa-fw fa-user-plus"></i> Actions list</B>. This tells the system who to contact and how depending on specific events.<BR><SPAN class="reason">[reason]</SPAN> is replaced with the message entered by the restaurant<BR><SPAN class="reason">[name]</SPAN> is replaced with the name of the party<BR><SPAN class="reason">[url]</SPAN> with a link to the receipt that doesn&apos;t require logging in<BR><SPAN class="reason">[sitename]</SPAN> with &apos;' . $site_name . '&apos;<BR>and the [tags] must be lower-cased');
            actionitem("order_placed", "the order is placed");
            actionitem("order_delivered", 'the <jump class="btn btn-sm btn-warning btn-border no-u">Delivered</jump> button is clicked');
            actionitem("order_confirmed", 'the <jump class="btn btn-sm btn-primary btn-border no-u">Confirmed</jump> button is clicked');
            actionitem("order_declined", 'the <jump class="btn btn-sm btn-border no-u">Declined</jump> or <jump class="btn btn-sm btn-danger btn-border no-u">Deleted</jump> buttons are clicked.');
            actionitem("user_registered", 'a new user is registered. (Since no restaurant is involved in this event, do not set the party of this event to the Restaurant)');

            if (read("profiletype") == 1) {
                newlist("Administrators");
                newitem("Escalating a user account to a restaurant", 'Go to <B><i class="fa fa-fw fa-user-plus"></i> Users list</B>, click the Profiletype column for that user, and click "Restaurant" from the drop-down menu');
                newitem("Changing the price of a topping or the delivery fee", 'Go to <B><i class="fa fa-fw fa-user-plus"></i> Additional_toppings list</B>, click the price column for that item, then change the text in the text box');
                newlist('<i class="fa fa-fw fa-user-plus"></i> Edit Menu');
                newitem("Size Costs", "Edit the cost of toppings for each size of pizza, and the delivery fee");
                newitem("Pizza Toppings/Wing Sauces", "Edit toppings/wing sauces, which category they belong to, if they are free toppings or not, and their group ID # (if the ID # is above 0, only 1 item from this group can be added to a menu item)");
                newitem("[New Category]", "Add a new menu item category to the list below");
                newitem("Category list", "Edit menu items. The Toppings/Wings_sauce numbers refer to how many lists of toppings they must select. ie: 2 Toppings would mean they have to select toppings for 2 pizzas");
            }
        }

        echo '</UL>';
    ?>


        <SPAN data-toggle="collapse" data-target="#terms" ID="item_terms">
            <H2 CLASS="title cursor-pointer">Terms of use</H2>
        </SPAN>
        <div id="terms" class="collapse">

            <INPUT TYPE="HIDDEN" ID="modaltitle" VALUE="{{ $site_name }} Terms and Conditions/Terms of Use">
            <p>This page outlines the Terms and Conditions in which {{ $site_name }} ('we' / 'us' / 'our' / '{{ $site_name }}') provide our services. {{ $site_name }} operates through our website
                www.{{ $site_name }} ('Website'). These terms of use apply to all users of the Website including users who upload any materials to the Website, users who use services provided through
                this Website, and users who simply view the content on or available through this website. Please read these terms carefully before ordering any products through the Website. By
                ordering products via this Website, you indicate you have read and accept these terms of use. Use of the Website is also subject to these terms of use. If you do not accept these terms
                of use, then do not use this Website or any of our content or services. We reserve the right to amend or update these terms from time to time without notice and the terms may have
                changed since your last visit to this Website. It is the users own responsibility to periodically review these terms of use for any changes. </p>

            <p>If you are accepting these terms of use on behalf a corporation or other entity, you represent and warrant that you have the necessary right and authority to enter into these terms of
                use on behalf of such corporation or entity and to bind such corporation or entity to these terms of use. </p>

            <?php /*p(); Our Service: The  $site_name service allows users to place "Meal of the Day" orders for food delivery or take out from participating restaurants identified on the website (the
                'Service'). You can use the Service from a computer or personal mobile device via an internet connection or data plan $site_name, and the restaurants offering service, are not
                responsible for any data or Internet usage fees. $site_name is an ordering service only, and does not prepare the food or provide delivery service. Therefore we are not liable
                for the actions or omissions of any third-party independent courier contractors or restaurants that provide services through our Service. This is not limited to but includes issues
                regarding food quality or timeliness of delivery. </p> */ ?>

            <?= p(); ?>Account Registration: You must create an account on the Website in order to place orders through the Service. Registration includes your delivery and payment information. As a
                registered user, you will have private access to member services such as the ability to review previous orders. All personal information provided by you will be handled in accordance
                with our Privacy Policy.</p>

            <?= p(); ?>Placing Your Order: Once you have selected the meal(s) of the day you wish to order from your chosen restaurant, you will provide any other required information and be given the
                opportunity to submit your order by clicking the 'place my order', or similar, button. Please review your order carefully as you CANNOT make changes to an order once it has been
                submitted. Your order CANNOT be cancelled once it has been submitted. </p>

            <?= p(); ?>Order Processing: Once you submit an order, it will be sent immediately to the restaurant from which you ordered. You are responsible to ensure all your account information such as
                order details, delivery address, billing, etc., is current, complete, and accurate.</p>

            <?= p(); ?>Order Delivery: When you place a pickup or delivery order, you may select the time you would like your order to be picked up or delivered to you. Please note this time is only an
                estimate and {{ $site_name }} offers no guarantee of this time. {{ $site_name }} will not be held responsible for any delays experienced in receiving your order. </p>

            <?= p(); ?>Price and Payment: All prices will be as quoted on the Website, and will have applicable sales taxes and delivery charges added upon before order submission. As selected by you
                during the ordering process, payment by cash, credit card, or debit card is due upon pickup or delivery. You consent to the collection and use of your information (including, if
                applicable, personal information) by such payment processing service as necessary to process your payments. You are responsible to ensure all your billing information is current,
                complete, and accurate. We will provide you with an online and/or emailed billing summary statement which you may review, save, or print at your discretion. </p>

            <?= p(); ?>Content: All information, photographs, video, sound, messages, or other materials, either publicly posted or privately transmitted to the Website by viewers or users ('User
                Content'), is the sole responsibility of the viewers or users. {{ $site_name }} does not actively monitor User Content and is in no way responsible for any User Content material that
                is uploaded, posted, emailed, transmitted or otherwise made available by using our Service. Therefore, we cannot guarantee the accuracy, integrity, or quality of such third-party
                content. By uploading or posting any User Content, you acknowledge that you own or have obtained legal rights to distribute all such content on the Website. You also
                grant {{ $site_name }} the right to display the content in the ordinary operation of its business. By using our Service, users acknowledge that they may be inadvertently exposed to
                content that is offensive, indecent or questionable. {{ $site_name }} will not be held liable in any way for any materials, including, but not limited to, for any errors or omissions
                in any materials or any defects or errors in any printing or manufacturing, or for any loss or damage of any kind incurred as a result of the viewing or use of any materials posted,
                emailed, transmitted or otherwise made available through the Service. </p>

            <?= p(); ?>Viewer/User Material Policy: All viewers and users are prohibited from uploading/transmitting or posting any material that:</p>

            <?= p(true); ?>is obscene, offensive or defamatory;</p>
            <?= p(true); ?>promotes discrimination or violence;</p>
            <?= p(true); ?>promotes illegal activity or substances; </p>
            <?= p(true); ?>invades another's privacy;</p>
            <?= p(true); ?>is used to impersonate another person or to misrepresent your affiliation with another person</p>
            <?= p(true); ?>gives the impression they originate from {{ $site_name }}</p>
            <?= p(true); ?>breaches any applicable local, national, or international law;</p>
            <?= p(true); ?>is fraudulent or unlawful;</p>
            <?= p(true); ?>amounts to unauthorized advertising</p>
            <?= p(true); ?>contains viruses, malware, or any harmful programs</p>
            <?= p(true); ?>interferes with or disrupts the Website or servers or networks connected to the Website, or disobeys any requirements, procedures, policies or regulations of networks connected to
                the Website, or probes, scans, or tests the vulnerability of any system or network, or breaches or circumvents any security or authentication measures</p>
            <?= p(true); ?>collects or stores any personal data about other viewers or users</p>

            <?= p(); ?>Restrictions on User Content and the Use of Our Service: {{ $site_name }} reserves the right to remove or refuse to distribute any User Content and to remove users or reclaim
                usernames. If your account is to be terminated due to a breach of the Terms of Use, you will be notified via email. We also reserve the right to access, read, preserve, and disclose
                any information as we reasonably believe is necessary to (i) satisfy any applicable law, regulation, legal process or government request, (ii) enforce these Terms of Use, including
                investigation of potential violations hereof, (iii) detect, prevent, or otherwise address fraud, security or technical issues, (iv) respond to user support requests, or (v) protect the
                rights, property or safety of our users and the public. </p>

            <?= p(); ?>License of User Content: By posting, submitting, and displaying User Content through our Service, you permit us a non-exclusive, royalty-free license (with the right to sublicense)
                to use, copy, modify, transmit, display, and distribute such User Content. {{ $site_name }} will not be responsible or liable for any use of any User Content in accordance with these
                Terms of Use. You acknowledge and warrant that you have all the rights, power, and authority necessary to grant the rights granted herein to any User content you submit.</p>

            <?= p(); ?>End User License: With the exception of User Content, this Website and the information and materials contained therein are the property of {{ $site_name }}, and are protected from
                unauthorized copying and dissemination by copyright law, and other intellectual property laws. You are allowed to use this Website and print and download extracts from the Website for
                your own personal non-commercial use. You agree not to misuse the Website (e.g. hacking and 'scrapping'), not to modify the digital or paper copies of any material that you download or
                print, and not to use any materials on the Website or the Website itself for commercial purposes. </p>

            <?= p(); ?>Customer Care: If you experience any issues with your Order, we recommend that you contact the Restaurant directly in the first instance to discuss the issue. We find this is the
                most effective way to resolve the issue. With that said, your patronage and support is very important to us. If you cannot reach the Restaurant or have trouble resolving the issue with
                the Restaurant, please contact us at {!! $email !!} within 48 hours of the incident. We will do our best to resolve the issue on your behalf but we cannot guarantee that we will be
                able to do so. </p>

            <?= p(); ?>Feedback: If you submit any suggestions, comments, or other feedback in regards to anything on the Website and/or Our Service, {{ $site_name }} may use such Feedback in the Website
                or Service. You agree that; (i) {{ $site_name }} is not subject to any confidentiality obligations in respect to the Feedback, (ii) the Feedback is not confidential or proprietary
                information of You or any third party and You have all the rights needed to disclose the Feedback to Us, (iii) {{ $site_name }} is free to use, reproduce, publicize, license,
                distribute, and otherwise commercialize the Feedback and (iv) You are not entitled to receive any reimbursement or compensation of any kind in respect of the Feedback. </p>

            <?= p(); ?>Advertising: By using the website, you acknowledge and agree that you may be exposed to advertisements. If you elect to have any dealings with the advertiser, you acknowledge and
                agree that {{ $site_name }} will not be held responsible or liable for any damages or losses you may incur as a result of such dealings. </p>

            <?= p(); ?>Links &amp; Third-Party Websites: The Website (including User Content) may contain links to third-party websites that {{ $site_name }} does not own or have control over. No
                reference to a third-party, product, or service offered by a third-party are endorsed or approved by {{ $site_name }}. By clicking the link provided by any third-party, you acknowledge
                and agree that you do so of your own choosing and {{ $site_name }} will not be responsible for any content provided by the third-party website. You are subject to the third-party's
                Privacy Policy and Term of Use which the user must review on their own. You hereby release {{ $site_name }} from all liability and/or damages that may arise from Your use of such
                websites or receipt of services from any such websites. </p>

            <p>{{ $site_name }} does not prohibit linking to third-party websites and content but does not wish to be linked to or from any third-party website which contains, posts or transmits any
                prohibited content in Section 8 of these Terms of Use. At any time, {{ $site_name }} reserves all rights to remove or prohibit (or require You to remove) any link to our Website for
                any reason. </p>

            <?= p(); ?>Termination: At our absolute discretion, we reserve the right to terminate or suspend your ability to access the Website. Reasons for termination can include, but are not limited
                to:</p>

            <?= p(true); ?>Breach or violation of these Terms of Use or any other agreement that You may have with {{ $site_name }} (including non-payment of any fees owed in connection with the website or
                otherwise owed by You to {{ $site_name }})</p>

            <?= p(true); ?>Requested by law enforcement or other government agencies</p>

            <?= p(true); ?>Unexpected technical, security, or legal issues or problems</p>

            <?= p(true); ?>Participation by You, directly or indirectly, in illegal or fraudulent activities</p>

            <?= p(true); ?>Requested by You</p>

            <?= p(); ?>Availability or Updates: At any time, {{ $site_name }} may alter, suspend, or discontinue the Website for any reason or no reason without notice. Occasionally the Website or Service
                may be unavailable due to maintenance or malfunction of computer/network equipment or other reasons. New or updated information and materials may be added during maintenance without
                prior notice.</p>

            <?= p(); ?>Data Loss: {{ $site_name }} takes measures to backup and ensure the integrity and availability of your content on the Website. However, in circumstances that are beyond the control
                of {{ $site_name }}, we are in no way responsible for lost content on your {{ $site_name }} account.</p>

            <p>If you have any questions regarding these Terms or if you wish to make any claim or complaint in regards to the Website or service, please contact us at {!! $email !!}</p>
            <br/>

            <h4>{{ $site_name }} Privacy Policy</h4>

            <p>We at {{ $site_name }} are committed to safeguarding the privacy of all visitors and users of this website or service via any web application, mobile application or any other platformor device. </p>

            <p>If you do not agree to any part of this privacy policy, please discontinue the use of the service and navigate away from this site immediately. </p>

            <p>By visiting or using our service, you accept and agree to this Privacy Policy, and consent to the collection, use and disclosure of your Personal Information accordingto {{ $site_name }} policies. </p>

            <p>The Personal Information We Collect:</p>

            <p>- Your name, address, telephone number, email address or other information to contact or identify you</p>

            <p>- Information about the goods or services provided to or by you</p>

            <p>- Information about your transactions with us, including bills, credit history, payment preference, billing and credit card information (credit card information is stored with <A HREF="https://stripe.com/ca">Stripe</A> for security purposes, and <B>NOT</B> on our own servers), and other details and preferences</p>

            <p>- Information from communications with you, including your feedback and requests for customer care</p>

            <p>- Location information of your mobile device when any one of our Apps is open on your device</p>

            <p>- Where required by law to do so;</p>

            <p>- Site activity information and cookies (Please refer to Use of Cookies)</p>

            <p>- Voluntary information provided by you, which may include restaurant reviews and ratings, referrals, special order instructions, feedback, and other actions performed on the Website or App</p>

            <p>Use and Disclosure of the Information:</p>

            <p>- To create and maintain a {{ $site_name }} account</p>

            <p>- To offer and provide licenses, products and services</p>

            <p>- To process orders and payments for products/services</p>

            <p>- To request customer feedback, in order to evaluate the needs, wants and satisfaction levels of our customers and to analyze and manage our business</p>

            <p>- To provide and administer services and monitor your purchases, fees paid and payable, payment history, parties to transactions, payments and payment card usage</p>

            <p>- To verify your identity as an authorized user concerning any accounts, and to implement, carry out and maintain security measures aimed at protecting our customers from identity
                theft, fraud and unauthorized access to accounts</p>

            <p>- To comply with legal or regulatory requirements (including those related to security);</p>

            <p>- To promote and market products and services offered by us;</p>

            <p>- To provide for the purchase and license to online products/services for purchase;</p>

            <p>- To respond to inquiries from you</p>

            <p>Since we utilize the services of third-party food service providers, we share your information to facilitate the Service. This may include sending emails or text notifications,
                processing payments, placing orders, sending delivery instructions, and other measures to help us in providing superior customer service. We require that our third-parties commit to
                use this information for the sole purpose of fulfilling the Service.</p>

            <p>{{ $site_name }} does not disclose any personal information to third parties without your prior consent unless required for the foregoing or as required or permitted by applicablelaw. </p>

            <p>Use of Cookies</p>

            <p>Cookies are small pieces of information which are issued to your computer when you visit a website. They are stored on your computer and contain a unique id number that identifies your
                web browser, and as used by {{ $site_name }}, do not impose any risk to the user. Cookies tell us which pages visitors have viewed and by how many people. They may also be used to
                improve user experience, such as remembering preferences on the site, or directing the user to favourite items and locations.</p>

            <p>The use of cookies is an industry standard and many major browsers are initially set up to accept them. You can refuse cookies by setting your browser to refuse to accept all cookies or
                to notify you when you have received a cookie. However, if you refuse to accept cookies, you may not be able to use some of the features available on the site.</p>

            <p>Security and Data Retention</p>

            <p>We know our legal obligations to protect personal information collected for our services, and we take appropriate steps to secure your information against unauthorized access,
                collection, use, disclosure, copying, modification, disposal, or destruction.</p>

            <p>As allowed by the law, we retain your personal information for as long as necessary for the purpose in which it was collected for. When the expiry date has been reached after an
                appropriate retention period, entirely at the discretion of {{ $site_name }}, your personal information will be either destroyed or made anonymous. Be aware that there may be legally
                required minimum retention periods we must observe.</p>

            <p>The transmission of information via the internet is not completely secure. Although we use current transmission encryption protocols, and take steps to protect your Personal
                Information, we cannot guarantee the security of your Personal Information or other data transmitted to the Website; any transmission is at your own risk. Once we have received your
                Personal Information, we will use organizational and technical safeguards to try to prevent unauthorized access. Please also note that the Website contains links to third party
                websites, which are not governed by this Privacy Policy, and {{ $site_name }} is not responsible for the collection, use or disclosure of Personal Information by such third party
                websites.</p>

            <p>Accessing and Updating Personal Information</p>

            <p>If you wish to see the Personal Information we hold about you, all requests must be submitted in writing (see below for contact information). Upon receipt of your written request, we
                will provide you with a copy of your Personal Information.*</p>

            <p>If you wish to update any Personal Information that is beyond the scope of your online account settings, all requests must be submitted in writing (see below for contact information).
                Upon receipt of your written request, we will update your information.*</p>

            <p>*For security reasons, we may request further proof of identification before any requests are processed.</p>

            <p>Changes to Our Privacy Policy</p>

            <p>We reserve the right to change/update our Privacy Policy at any time. Any changes to our Privacy Policy will be posted to this website. </p>

            <p>Contact Information</p>

            <p>If you have any questions, concerns or comments, or would like access to your information, or would like to update your information, please contact us at {!! $email !!}.</p>
            <br>

            <h4>Allergy & Dietary Information</h4>
            <p>{{ $site_name }} tries to accurately copy the description of dishes.</p>
            <p>However, it is the restaurants that prepares the dishes, please use the notes section on the receipt to specify particular allergies.</p>
            <p>If you are in any doubt about the presence of allergens, you should confirm with the restaurant.</p>
            <p>Last updated: February 15, 2017</p>
        </div>
    </DIV>
    </DIV>
    <div class="btn-group" style="display: none">
    <button id="gototop" class="btn btn-sm btn-primary "><A HREF="#top"><i class="fa fa-arrow-up"></i> Go to the top</A></button>
    <button id="expandall" class="btn btn-sm btn-primary footer" onclick="expandall(true);"><i class="fa fa-expand"></i> Expand all</button>
    <button id="contractall" class="btn btn-sm btn-primary footer" onclick="expandall(false);"><i class="fa fa-compress"></i> Contract all</button>
    <button id="gotobottom" class="btn btn-sm btn-primary footer"><A HREF="#bottom"><i class="fa fa-arrow-down"></i> Go to the bottom</A></button>
    </div>
@endsection


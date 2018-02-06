<?php
    startfile("popups_login");
    $Additional_Toppings = first("SELECT * FROM additional_toppings", false);
    function getVal($Additional_Toppings, $size) {
        $it = getiterator($Additional_Toppings, "size", $size, false);
        return $Additional_Toppings[$it]["price"];
    }
    $minimum = number_format(getVal($Additional_Toppings, "Minimum"), 2);
    $delivery = number_format(getVal($Additional_Toppings, "Delivery"), 2);
    $time = getVal($Additional_Toppings, "DeliveryTime");
    $hours = first("SELECT * FROM hours WHERE restaurant_id = 0");
?>
<div class="row">
    <DIV CLASS="col-lg-4 col-md-5 bg-white">
        <DIV CLASS="btn-sm-padding bg-white" style="padding-bottom: 1rem !important;padding-top: .5rem !important;">
            <ul class="nav nav-tabs mb-1" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" href="#profile" role="tab" data-toggle="tab" id="logintab" onclick="skiploadingscreen = false;" style="font-weight: bold">LOG IN</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#buzz" role="tab" data-toggle="tab" id="signuptab" onclick="skiploadingscreen = true;" style="font-weight: bold">SIGN UP</a>
                </li>
            </ul>
            <!-- Tab panes -->
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane fade in active" id="profile">
                    <div class="input_left_icon"><span class="fa-stack fa-2x"><i class="fa fa-circle fa-stack-2x"></i><i class="fa fa-envelope text-white fa-stack-1x"></i></span></div>
                    <div class="input_right">
                        <INPUT TYPE="text" id="login_email" placeholder="Email" class="form-control session_email_val" onkeydown="enterkey(event, '#login_password');" required>
                    </div>
                    <div class="input_left_icon"><span class="fa-stack fa-2x"><i class="fa fa-circle fa-stack-2x"></i><i class="fa fa-lock text-white fa-stack-1x"></i></span></div>
                    <div class="input_right">
                        <INPUT TYPE="password" id="login_password" placeholder="Password" class="form-control" onkeydown="enterkey(event, 'login');" required>
                    </div>
                    <div class="clearfix py-2"></div>
                    <BUTTON CLASS="btn-block btn btn-primary" href="#" onclick="handlelogin('login');">LOG IN</BUTTON>
                    <div class="clearfix py-2"></div>

                    <BUTTON CLASS="btn-block btn-sm btn btn-link" href="#" style="color: #dadada !important;" onclick="handlelogin('forgotpassword');">FORGOT PASSWORD</BUTTON>
                </div>
                <div role="tabpanel" class="tab-pane fade" id="buzz">
                    <FORM id="addform">
                        <?php
                            if (!read("id")) {
                                echo view("popups_address", array("style" => 1, "required" => true, "icons" => true, "firefox" => false))->render();
                            }
                        ?>
                    </FORM>
                    <FORM Name="regform" id="regform">
                        <?= view("popups_edituser", array("phone" => false, "autocomplete" => "new-password", "required" => true, "icons" => true))->render(); ?>
                    </FORM>
                    <div class="clearfix py-2"></div>

                    <button class="btn btn-block btn-primary" onclick="register();">
                        SIGN UP
                    </button>
                </div>
            </div>
            <DIV CLASS="clearfix"></DIV>
        </DIV>

        <div class="py-3">
            <center>
                <img src="<?= webroot("images/delivery.jpg"); ?>" style="width: 50%;"/>
                <h2 class="text-danger" style="text-align: center;">Only the Best Pizza in <?= cityname; ?></h2>
                ${{ $minimum }} Minimum<br>
                ${{ $delivery }} Delivery<br>
                Credit/Debit Only
            </center>
        </div>

    </DIV>
    <div class="col-lg-8 col-md-7 bg-white py-2 bg-inverse" style="border: .75rem solid transparent !important">
        <div class="btn-sm-padding" style="border-radius: 0;background: transparent !important;"><br>
            <span style=";font-size: 2.5rem; font-weight: bold;line-height: 3.1rem;"> <?= strtoupper(cityname); ?> PIZZA DELIVERY</span>
            <br>
            <br>
            <p>The art of delivery is in the team, local restaurants at your footstep in <?= $time; ?> minutes.</p>
            <p class="lead strong">HOURS OF OPERATION</p>
            <TABLE>
                <?php
                    $daysofweek = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
                    for ($day = 0; $day < 7; $day++) {
                        echo '<TR><TD>' . $daysofweek[$day] . "&nbsp;&nbsp;&nbsp; </TD>";
                        $open = $hours[$day . "_open"];
                        $close = $hours[$day . "_close"];
                        if ($open == "-1" || $close == "-1") {
                            echo '<TD COLSPAN="2"">Closed';
                        } else {
                            echo '<TD>' . GenerateTime($open) . ' to </TD><TD>' . GenerateTime($close);
                        }
                        echo '</TD></TR>';
                    }
                ?>
            </TABLE>
            <br>
            <i class="lead text-danger strong">"FASTER THAN PICKING UP THE PHONE!"</i><br><br>
            <a class="btn-link" href="<?= webroot("help"); ?>" role="button">LEARN MORE</a>
        </div>
    </div>
</div>
<SCRIPT>
    redirectonlogin = true;
    var minlength = 5;
    var getcloseststore = false;
    lockloading = true;
    blockerror = true;

    function register() {
        if (isvalidaddress()) {
            $("#reg_address-error").remove();
        } else if ($("#reg_address-error").length == 0) {
            $('<label id="reg_address-error" class="error" for="reg_name">Please check your address</label>').insertAfter("#formatted_address");
        }
        redirectonlogin = false;
        $('#regform').submit();
    }

    $(".session_email_val").on("keydown", function (e) {
        return e.which !== 32;
    });

    $(function () {
        $("form[name='regform']").validate({
            rules: {
                name: "required",
                formatted_address: {
                    validaddress: true,
                    required: true
                },
                email: {
                    required: true,
                    email: true,
                    remote: {
                        url: '<?= webroot('public/user/info'); ?>',
                        type: "post",
                        data: {
                            action: "testemail",
                            _token: token,
                            email: function () {
                                return $('#reg_email').val();
                            },
                            user_id: "0"
                        }
                    }
                },
                password: {
                    minlength: minlength
                },
                /*phone: {
                 phonenumber: true
                 }*/
            },
            messages: {
                name: "Please enter your name",
                password: {
                    required: "Please provide a password",
                    minlength: "Your new password must be at least " + minlength + " characters long"
                },
                email: {
                    required: "Please enter an email address",
                    email: "Please enter a valid email address",
                    remote: "Please enter a unique email address"
                }/*,
                 phone: {
                 required: "Please enter a cell phone number",
                 phonenumber: "Please enter a valid cell phone number"
                 }*/
            },
            submitHandler: function (form) {
                if (!isvalidaddress()) {
                    return false;
                }
                var formdata = getform("#regform");
                formdata["action"] = "registration";
                formdata["_token"] = token;
                formdata["address"] = getform("#addform");
                $.post(webroot + "auth/login", formdata, function (result) {
                    if (result) {
                        try {
                            var data = JSON.parse(result);
                            $("#logintab").trigger("click");
                            $("#login_email").val(formdata["email"]);
                            $("#login_password").val(formdata["password"]);
                            redirectonlogin = true;
                            handlelogin('login');
                        } catch (e) {
                            alert(result, "Registration");
                        }
                    }
                });
                return false;
            }
        });
    });
    $(document).ready(function () {
        $("#profile").removeClass("fade").removeClass("in");
    });
</SCRIPT>
<?php endfile("popups_login"); ?>
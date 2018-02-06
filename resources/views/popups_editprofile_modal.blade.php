<!-- edit profile Modal -->
<div class="modal" id="profilemodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="myModalLabel" class="align-middle">My Profile</h2>
                <button data-popup-close="profilemodal" data-dismiss="modal" class="btn btn-sm ml-auto align-middle bg-transparent"><i class="fa fa-close"></i></button>
            </div>

            <div class="modal-body">
                <FORM NAME="user" id="userform">
                    @include("popups_edituser", array("showpass" => true, "email" => false, "icons" => true))

                    <div class="input_left_icon"><span class="fa-stack fa-2x"><i class="fa fa-circle fa-stack-2x"></i><i class="fa fa-envelope text-white fa-stack-1x"></i></span></div>

                    <div class="input_right"><input type="text" readonly class="form-control session_email_val"></div>

                    <DIV class="clearfix mt-1"></DIV>
                    <DIV CLASS="error" id="edituser_error"></DIV>
                    <DIV class="clearfix mt-1"></DIV>

                    <BUTTON CLASS="btn-link pull-right" onclick="return userform_submit(true);">SAVE</BUTTON>
                </FORM>


                <div CLASS="editprofilediv">
                    <DIV ID="addresslist"></DIV>
                </div>
                <hr>
                <div CLASS="editprofilediv">
                    <DIV ID="cardlist"></DIV>
                </div>

                <div class="alert alert-info mt-3 mb-0" style="font-size: .85rem">
                    > Add a new address on checkout
                    <br> > Add a new credit/debit card on checkout
                    <br> > <a href="help" class="btn-link">MORE INFO</a>
                </div>
                <div CLASS="editprofilediv mt-2 dont-show">
                    <button ONCLICK="handlelogin('logout');" CLASS="btn btn-primary pull-left" href="#">LOG OUT</button>
                    <button ONCLICK="orders();" CLASS="btn btn-primary pull-right" href="#">PAST ORDERS</button>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<!-- end edit profile Modal -->
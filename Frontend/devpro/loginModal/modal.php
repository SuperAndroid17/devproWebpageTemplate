<!-- loginModal -->
      <div class="modal fade" id="loginModal">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title">DevPro Login</h4>
            </div>
            <div class="modal-body">
                <form id="devproLogin" action="http://ygopro.de/web-devpro/Engine/Api/getjson.php">
                <div class="form-group">
                    <input id="devproEmail" name="devproUsername" type="text" placeholder="Username" class="form-control">
                </div>
                <div class="form-group">
                    <input id="devproPassword" name="devproPassword" type="password" placeholder="Password" class="form-control">
                </div>
                    <div id="captchaContainer" class="g-recaptcha" data-sitekey="6Lcq4AMTAAAAAJO4gf_ueF9ZKq8uDwwaiBa7yFdg"></div>   
              
            
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-success">Sign in</button>
              <p id="wronglogin" style="color: red;"></p>
              </form>
            </div>
          </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
      </div><!-- /.modal -->
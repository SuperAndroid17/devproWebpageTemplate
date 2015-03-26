<nav class="navbar navbar-inverse navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
         
          
          <!-- Nav Menu Punkte -->
          <ul class="nav navbar-nav">
        <li><a href="http://ygopro.de/web-devpro/">Home</a></li>
        
                <!-- Dropdown Ranglisten-->
                <li role="presentation" class="dropdown">
                <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-expanded="false">Rankings <span class="caret"></span></a>
                <ul class="dropdown-menu" role="menu">
                    <li><a href="http://ygopro.de/web-devpro/index.php?site=singleRanking">Single</a></li>
                    <li><a href="http://ygopro.de/web-devpro/index.php?site=matchRanking">Matches</a></li>
                </ul>
              </li>
              
        
        <li id="devproDashboard" role="presentation" class="disabled"><a href="http://ygopro.de/web-devpro/index.php?site=Dashboard">Dashboard</a></li>
          </ul><!-- END Nav Menu Punkte -->
          
          
        </div>
        <div id="navbar" class="navbar-collapse collapse">
            <form id="devproLoginNavbar" class="navbar-form navbar-right">
              <script>
                  // check if user is logged and Display login or logout button
                  getSessionStatus();
              </script>
           
            
          </form>
        </div><!--/.navbar-collapse -->
      </div>
    </nav>
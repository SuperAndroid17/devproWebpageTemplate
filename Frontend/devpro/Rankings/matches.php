<!--
 singles.php shows the single Ranking from Elo
 Show different Top Rankings and also a Username Ranking by search
-->
<div class="jumbotron">
      <div class="container">
        <h1>Match Rankings</h1>
        <p>View the Top Rankings!</p>
        
        
      </div>
    </div>

<div class="container">
    
    
    <form id="devproShowMatchRankings" class="form-inline" method="post" action="http://ygopro.de/web-devpro/Engine/Api/getjson.php">
        <div class="form-group">
            <select class="form-control" name="ShowMatchRankings">
                <option value="100">Top 100</option>
                <option value="200">Top 200</option>
                <option value="300">Top 300</option>
          </select>
        </div>
        <button type="submit" class="btn btn-default">show Ranking</button>
   </form>
    
    <div id="singleRankingTable"></div>
    
</div>
<?
session_start() ;
/* PHP external files */
require_once('/home/sterlid2/Private/sysNotification.php');
require_once('functions.php');

/* Temp Variables */
$user = "User"; // Taken from DB, user account name
$totalAssets = "0.00"; // Sum of all of users' accounts
$lastVisit = date("F j, Y, g:i a"); // Last time of login
//$accounts = ["Checking", "Savings", "Account 3", "Account 4", "Account 5"]; // User Account names taken from DB

/* Main Variables */

//$db creates a connection to the main database
@ $db = DBconnect();
//this block queries for the number of total account a client has
$query = 'select count(*) as Total from accountDirectory';
$result = $db->query($query);
$row = mysqli_fetch_object($result) ;

$amountOfAccounts = $row->Total; // This 'Total' variable is taken from the DB (Total amount of accounts the client has)
$amountOfTransactions = 5; // Number of recent transactions to show
?>

<!DOCTYPE html>
<html lang="en-US">
	<head>
		<title>Home</title>
		<link rel="stylesheet" href="/~sterlid2/TemporaryBank/web/CSS/stylesheet.css"> <!-- Stylesheet -->
        <link rel="preconnect" href="https://fonts.googleapis.com"> <!-- Google Font -->
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin> <!-- Google Font -->
        <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;1,100&display=swap" rel="stylesheet"> <!-- Google Font -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css"> <!-- Svg Icons -->
		<meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Different screen size scaling compatability -->
	</head>
	<body>
		<nav class="menubar">
			<ul class="menugroup">
				<li class="menulogo"><a href="/~sterlid2/bank/home.php">TempBank</a></li>
                <li class="menutoggle"><a href="#"><i class="fas fa-bars"></i></a></li>
				<li class="menuitem"><a href="/~sterlid2/bank/home.php">Home</a></li>
				<li class="menuitem"><a href="/~sterlid2/bank/account/transfer.php">Transfer</a></li>
				<li class="menuitem"><a href="/~sterlid2/bank/account/payments.php">Payments</a></li>
				<li class="menuitem"><a href="/~sterlid2/bank/account/open.php">Open New Account</a></li>
				<li class="menuitem submenu">
				    <a tabindex="0">Statements</a>
				    <!--<ul class="submenugroup">
				        <li class="subitem"><a href="#PrintAll">Print Statement</a></li>
				        <li class="subitem"><a href="#PrintOne">Print Specific</a></li>
				    </ul>-->
				</li>
			</ul>
			<ul class="menugroup">
				<li class="menuitem"><a href="/~sterlid2/bank/user/options.php">Options</a></li>
				<li class="menuitem"><a href="/~sterlid2/bank/login.php">Sign Out</a></li>
			</ul>
		</nav>
		<? notification(); ?>
		<div class="container flex-center">
		    <div class="list main">
		        <h2 id="title">Welcome, <? echo $user ?></h2>
		        <div class="split">
		            <label class="info">Available Accounts</label>
		            <a href="/~sterlid2/bank/account/open.php" class="expand-button transform-button extend-left round shadow">
		                <div class="split">
		                    <div class="animate-left">
            		            <div class="toggle-button">
            		                <p class="expanded-info">Add Account</p>
            		            </div>
        		            </div>
		                    <p class="condensed-info animate-rotate90"><b>+</b></p>
		                </div>
		            </a>
		        </div>
		        <?
	        	$accounts = []; // User Account numbers taken from DB
	        	$accounts_Names = []; // User Account names taken from DB
	        	$accounts_Balance = []; //balance for each account of the client. the indexes correspond to the index of accounts[] and accounts_Balance[]
                //$query = 'select accountType from accountDirectory where clientID=123456';
                $total_balance = 0;
                $query = 'select * from accountDirectory where clientID=123456';
                $result = $db->query($query);
                $rows = $result->fetch_all(MYSQLI_ASSOC);
                foreach($rows as $acc) {
                    $accounts[] = $acc['accountNum'];
                    $accounts_Names[] = $acc['accountType'];
                    
                }
	            for ($i = 0; $i < $amountOfAccounts; $i++) {
	               
	                $BalanceQuery = 'select balance from '.$accounts_Names[$i].' where accountNum='.$accounts[$i];
                    $BalanceQueryResult = $db->query($BalanceQuery);
                    //$Balance = fetch_object()->balance;
                    $Balance = mysqli_fetch_object($BalanceQueryResult);
                    if($accounts_Names[$i]=='credit'){
                        $total_balance -= $Balance->balance;
                        $account_message = "Credit Used";
                    } else{
                        $total_balance += $Balance->balance;
                        $account_message = "Available";
                    }
                    
                    $acc_num=substr($accounts[$i], -4);
		            echo "<a href=\"/~castilg1/Software Engineering 2/details.php?acc=".$accounts_Names[$i]."&num=".$acc_num."\" class=\"big-color-button transform-button split round shadow\">
                            <div class=\"list\">
            		            <p class=\"focused-info\">$accounts_Names[$i]</p>
            		            <p>$accounts_Names[$i] account (*".substr($accounts[$i], -4).")</p>
        		            </div>
        		            <div class=\"split animate-left\">
            		            <div class=\"list text-right\">
            		                <p>".$account_message."</p>
                		            <p class=\"focused-info\">\$".$Balance->balance."</p>
            		            </div>
            		            <div class=\"toggle-button\">
            		                <i class=\"fas fa-chevron-right\"></i>
            		            </div>
        		            </div>
            		    </a>";
                }
		        ?>
		    </div>
		    <div class="list sub">
		        <div class="container round shadow">
    		        <div class="item-banner top-round">
    		            <!--<label class="banner-text">Account Details</label>-->
    		            <h2 class="big text-center">Total Balance: $<?php echo $total_balance ?></h2>
    		        </div>
    		        <div class="item-content bottom-round">
    		            <p class="info text-center">Last Sign In: <? echo $lastVisit ?></p>
    		        </div>
    		    </div>
		        <div class="container round shadow">
    		        <div class="item-banner top-round">
    		            <label class="banner-text"> Recent Activity</label>
    		        </div>
    		        <div class="item-content bottom-round">
    		            <?
    		            
    		            $recentAccount = "Checking"; // temp
    		            
    		            $transTime = [];
    		            $transAmount = [];
    		            
    		            $transactionQuery = "SELECT * FROM transactions WHERE clientID=123456 limit ".$amountOfTransactions;
    		            $result = $db->query($transactionQuery);
                        $rows = $result->fetch_all(MYSQLI_ASSOC);
                        foreach($rows as $transaction) {
                            $transTime[] = $transaction['transactionTime'];
                            $transAmount[] = $transaction['transactionAmount'];
                        }
                        
		                for ($n = 1; $n <= $amountOfTransactions; $n++) {
		                    $index=$n-1;
		                    echo "<a href=\"/~castilg1/Software Engineering 2/details.php?acc=".$recentAccount."\" class=\"highlight-button transform-button split round\">
		                            <div class=\"list-padded\">
		                                <h3 class=\"bold\">Transaction $n</h3>
		                                <p>$transTime[$index]<p>
		                            </div>
		                            <div class=\"split animate-left\">
		                                <div class=\"list-padded text-right\">
		                                    <h3>$transAmount[$index]</h3>
		                                    <p>Payment</p>
		                                </div>
                       		            <div class=\"toggle-button\">
                        		            <i class=\"fas fa-chevron-right\"></i>
                        		        </div>
		                            </div>
		                          </a>";
		                    
		                    if ($n != $amountOfTransactions){
		                        echo "<hr>";
		                    }
		                }
    		            ?>
    		        </div>
    		    </div>
		        <div class="container round shadow">
    		        <div class="item-banner top-round">
    		            <label class="banner-text">Quick Payments</label>
    		        </div>
    		        <form id="payments" class="item-content bottom-round">
        		        <label class="info" for="PayTo">Pay To</label>
    		            <div class="form-item">
        		            <select id="PayTo" class="input-field">
                                <?
                                for ($i = 0; $i < $amountOfAccounts; $i++) {
                                    echo "<option>Recipient $i</option>";
                                }
                                ?>
        		            </select>
    		            </div>
        		        <label class="info" for="PayFrom">Pay From</label>
    		            <div class="form-item">
        		            <select id="PayFrom" class="input-field">
                                <?
                                for ($i = 0; $i < $amountOfAccounts; $i++) {
                                    echo "<option>$accounts[$i]</option>";
                                }
                                ?>
        		            </select>
        		        </div>
    		            <label class="info" for="Date">Date</label>
    		            <div class="form-item">
        		            <input id="Date" class="input-field" type="date" placeholder="yyyy-mm-dd">
    		            </div>
    		            <label class="info" for="Amount">Amount</label>
    		            <div class="form-item">
        		            <input id="Amount" class="input-field" type="number" min="0" max="<? echo $totalAssets ?>">
    		            </div>
    		            <div class="form-item">
                            <button form="payments" class="standard-button transform-button flex-center round">
                                <div class="split">
                                    <p class="animate-left">Schedule Payment<p>
                   		            <div class="toggle-button">
                    		            <i class="fas fa-chevron-right"></i>
                    		        </div>
                                </div>
                            </button>
    		            </div>
    		        </form>
    		    </div>
		    </div>
		</div>
	</body>
	<script type="text/javascript" src="/~sterlid2/bank/Scripts/navigation.js">
	</script>
	<script type="text/javascript" src="/~sterlid2/bank/Scripts/post.js">
	</script>
	<?php
	    $db -> close();
	?>
</html>

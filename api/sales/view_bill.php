<?php
require_once "../session.php";
$user_id=$_SESSION['user_id'];

if (!isset($_GET['entry_no'])) die("Entry missing");

$entry_no = (int)$_GET['entry_no'];

/* =============================
   BILL + PARTY DETAILS
============================= */
$sql = "
SELECT 
sm.entry_no,
sm.entry_date,
sm.remark,
sm.grand_total,
sm.payment_mode,

p.party_name,
p.city,
p.state,
p.area,
p.pin_code,
p.contact_no,
p.gst_no,
p.pan_no,
p.email

FROM sales_master sm
JOIN party p ON p.party_id = sm.party_id
WHERE sm.entry_no = $entry_no AND sm.user_id = $user_id
";

$res = mysqli_query($con,$sql);
if(mysqli_num_rows($res)==0) die("Bill not found");

$bill = mysqli_fetch_assoc($res);

/* =============================
   ITEMS
============================= */
$items=[];
$itemRes = mysqli_query($con,"
SELECT product_name, quantity, rate, amount
FROM sales_items
WHERE entry_no=$entry_no AND user_id=$user_id
");

while($r=mysqli_fetch_assoc($itemRes)){
$items[]=$r;
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Invoice <?= $bill['entry_no'] ?></title>

<style>
body{
font-family:Arial;
margin:0;
padding:30px;
background:#fff;
color:#111;
}

.invoice{
max-width:900px;
margin:auto;
border:1px solid #ddd;
padding:25px;
}

.header{
display:flex;
justify-content:space-between;
margin-bottom:25px;
}

.company{
font-size:22px;
font-weight:700;
}

.meta{
text-align:right;
font-size:14px;
}

.party-box{
border:1px solid #ddd;
padding:15px;
margin-bottom:20px;
font-size:14px;
line-height:1.6;
}

table{
width:100%;
border-collapse:collapse;
margin-top:10px;
}

th,td{
border:1px solid #ddd;
padding:10px;
text-align:center;
}

th{
background:#111;
color:#fff;
}

.total{
margin-top:20px;
display:flex;
justify-content:flex-end;
}

.total-box{
background:#111;
color:#fff;
padding:12px 20px;
font-weight:700;
}

.footer{
margin-top:35px;
font-size:13px;
opacity:.7;
}

.print-btn{
position:fixed;
top:20px;
right:20px;
background:#111;
color:#fff;
padding:10px 14px;
border:none;
border-radius:8px;
cursor:pointer;
}

@media print{
.print-btn{display:none;}
}
</style>
</head>

<body>

<button class="print-btn" onclick="window.print()">Print</button>

<div class="invoice">

<div class="header">
<div class="company">SoftBrokerage Invoice</div>

<div class="meta">
Bill No: <b><?= $bill['entry_no'] ?></b><br>
Date: <?= $bill['entry_date'] ?><br>
Payment: <?= $bill['payment_mode'] ?>
</div>
</div>

<div class="party-box">
<b>Party Details</b><br>
<?= $bill['party_name'] ?><br>
<?= $bill['area'] ?>, <?= $bill['city'] ?>, <?= $bill['state'] ?> - <?= $bill['pin_code'] ?><br>
Contact: <?= $bill['contact_no'] ?><br>
Email: <?= $bill['email'] ?><br>
GST: <?= $bill['gst_no'] ?> | PAN: <?= $bill['pan_no'] ?>
</div>

<table>
<thead>
<tr>
<th>#</th>
<th>Product</th>
<th>Qty</th>
<th>Rate</th>
<th>Amount</th>
</tr>
</thead>
<tbody>

<?php foreach($items as $i=>$it): ?>
<tr>
<td><?= $i+1 ?></td>
<td><?= $it['product_name'] ?></td>
<td><?= $it['quantity'] ?></td>
<td><?= $it['rate'] ?></td>
<td><?= $it['amount'] ?></td>
</tr>
<?php endforeach; ?>

</tbody>
</table>

<div class="total">
<div class="total-box">
Grand Total ₹ <?= number_format($bill['grand_total'],2) ?>
</div>
</div>

<div class="footer">
Remark: <?= $bill['remark'] ?>
</div>

</div>

</body>
</html>

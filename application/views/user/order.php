<?php
if (!isset($_SESSION['email'])) {

    redirect(base_url() . 'User/signup');
}
?>

<!DOCTYPE html>
<html lang="en">
<?php require(dirname(__DIR__) . "/myHead.php"); ?>

<body>

    <?php require(dirname(__DIR__) . "/myHeader.php"); ?>

    <div class="login-bg">
        <div class="login-color-bg">
            <h1>Order History</h1>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <div class="col-lg-3 mt-5">

                <div class="user-aside-con">
                    <ul class="user-aside">
                        <li><a href="<?php echo base_url() . "User/profile"; ?>"><i class="fa fa-caret-right" aria-hidden="true"></i> Account Information</a></li>
                        <li><a href="<?php echo base_url() . "User/order"; ?>" class="color-purple"><i class="fa fa-caret-right color-purple" aria-hidden="true"></i> My Orders</a></li>
                        <li><a href="<?php echo base_url() . "User/wishlist"; ?>"><i class="fa fa-caret-right" aria-hidden="true"></i> My Wishlist</a></li>
                        <li><a href="<?php echo base_url() . "User/newsletter"; ?>"><i class="fa fa-caret-right" aria-hidden="true"></i> My Newsletter</a></li>
                        <li><a href="<?php echo base_url() . "User/changepassword"; ?>"><i class="fa fa-caret-right" aria-hidden="true"></i> Change Password</a></li>
                        <li><a href="<?php echo base_url() . "User/logout"; ?>"><i class="fa fa-caret-right" aria-hidden="true"></i> Logout</a></li>
                    </ul>
                </div>

            </div>
            <div class="col-lg-9">

                <?php if (count($userOrders) > 0) { ?>

                    <div class="cust-pages-container mt-5">
                        <h2>MY Order</h2>

                        <div class="table-responsive order-history-table">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Order Number</th>
                                        <th scope="col">Order Date</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">Total Price</th>
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    <?php $count = 1;
                                        foreach ($userOrders as $order) { ?>
                                        <tr>
                                            <td scope="row"><?php echo $count; ?></td>
                                            <td>175525 <a href="<?php echo base_url() . "User/orderDetail/" . $order['orderId']; ?>" class="color-purple text-decoration-none">View Detail</a></td>
                                            <td><?php echo date('d-m-Y', strtotime($order['orderDate'])); ?></td>
                                            <td><?php echo $order['status']; ?></td>
                                            <td><?php echo "$" . $order['cartGrandTotal'].".00"; ?></td>
                                            <td>
                                                <div class="btn register-btn shopping-cart-btn"><a href="#" class="text-decoration-none color-white">Reorder</a></div>
                                            </td>
                                        </tr>

                                    <?php $count++;
                                        } ?>
                                </tbody>
                            </table>
                        </div>
                        <a href="#" class="btn register-btn">Clear History</a>
                    </div>
                <?php } else { ?>
                    <div class="cust-pages-container mt-5">
                        <h2>You have no order history</h2>
                    </div>
                <?php } ?>
            </div>


        </div>
    </div>





    <!--Subscribe Section-->
    <div class="container-fluid subscribe-sec">

        <div class="container">
            <div class="row">
                <div class="col-md-8 d-flex align-items-center">
                    <form action="#" style="width: 100%;">
                        <div class="container-fluid">
                            <div class="row d-flex align-items-center">
                                <div class="col-md-3">
                                    <img src="<?php echo base_url(); ?>images/myImages/envelope.png" class="img-fluid envelope-img"><span class="news">NEWSLETTER</span>
                                </div>
                                <div class="col-md-5">
                                    <input type="text" placeholder="YOUR EMAIL ADDRESS" class="subs-input" />
                                </div>
                                <div class="col-md-2">
                                    <input type="submit" class="btn subs-btn" value="SUBSCRIBE" />
                                </div>
                            </div>
                        </div>


                    </form>
                </div>
                <div class="col-md-4">
                    <div style="position: relative">
                        <img src="<?php echo base_url(); ?>images/myImages/kids.png" class="img-fluid">
                        <div class="social-icons">
                            <img src="<?php echo base_url(); ?>images/myImages/fb.png" class="img-fluid">
                            <img src="<?php echo base_url(); ?>images/myImages/insta.png" class="img-fluid">
                            <img src="<?php echo base_url(); ?>images/myImages/skype.png" class="img-fluid">
                            <img src="<?php echo base_url(); ?>images/myImages/twitter.png" class="img-fluid">
                        </div>
                    </div>

                </div>
            </div>
        </div>


    </div>

    <!--Subscribe Section ends-->

    <?php require(dirname(__DIR__) . "/myFooter.php"); ?>

</body>

</html>
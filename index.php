<?php
    session_start();

    require __DIR__ . '/vendor/autoload.php';

    use phpish\shopify;

    require __DIR__ . '/conf.php';

    $shopify = shopify\client(SHOPIFY_SHOP, SHOPIFY_APP_API_KEY, SHOPIFY_APP_PASSWORD, true);

    $countries = $shopify('GET /admin/countries.json', array());
    //print_r($countries);
    //die;

    $products = $shopify('GET /admin/products.json', array('published_status' => 'published'));
    //print_r($products);die;
?>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Example of Bootstrap 3 Accordion</title>
        <link rel="stylesheet" href="bootstrap.min.css">
        <link rel="stylesheet" href="bootstrap-theme.min.css">
        <link rel="stylesheet" href="style.css">
        <script src="jquery.min.js"></script>
        <script src="bootstrap.min.js"></script>
        <script src="javascript.js"></script>
    </head>
    <body>
        <nav class="navbar navbar-inverse menu">
            <div class="container-fluid">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="http://localhost/new_prj/">MetaField's for country</a>
                </div>
                <div class="collapse navbar-collapse" id="myNavbar">
                    <ul class="nav navbar-nav navbar-right">
                        <!--<li><a href="#"><span class="glyphicon glyphicon-user"></span> Sign Up</a></li>-->
                        <li><a href="#"><span class="glyphicon glyphicon-log-in"></span> Login</a></li>
                    </ul>
                </div>
            </div>
        </nav>
        <div class="container content">
            <div class="row">
                <div class="col-sm-8">
                    <!--tttttttttttttttttttttttttttttt-->
                    <?php
                    foreach ($products as $value) {
                        $id = $value['id'];
                        $title = $value['title'];
                        $image = $value['images'][0]['src'];
                        $metafields = $shopify('GET /admin/products/'.$id.'/metafields.json', array());
                        ?>
                        <div class="panel-group" id="accordion" role="tablist">
                            <div class="panel panel-default">
                                <form action = "<?php $_PHP_SELF ?>" method = "GET">
                                    <input type="hidden" name="product_id" value="<?php echo $id ?>" />
                                    <div class="panel-heading" role="tab" id="headingOne">
                                        <h4 class="panel-title">
                                            <a role="button" data-toggle="collapse" data-parent="#accordion" href="#<?php echo $id; ?>" aria-controls="collapseOne">
                                                <?php echo $title ?>
                                            </a>
                                        </h4>
                                    </div>
                                    <div id="<?php echo $id; ?>" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
                                        <div class="panel-body">
                                            <div class="col-md-8 once">
                                                <h4>Select Country For This Product</h4><br>
                                                <select class="form-control" name="cntry">
                                                    <option value="none" selected="selected">Select Country</option>
                                                    <?php
                                                    foreach ($countries as $value) {
                                                        $country = $value['name'];
                                                        foreach ($metafields as $value) {
                                                            $country_value = $value['value'];
                                                        ?>
                                                        <option value="<?php echo $country ?>" <?php if($country_value == $country ) { ?> selected <?php } ?>><?php echo $country ?></option>
                                                    <?php }
                                                        } ?>
                                                </select>
                                                <input class="btn btn-primary devat" type = "submit" value="Save"/><br>
                                            </div>
                                            <div class="col-md-4">
                                                <img id="image" src="<?php echo $image; ?>" alt="<?php echo $title; ?>" height="200" width="200" />
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <?php
                    }
                    if ($_GET["cntry"] && $_GET["product_id"]) {
                        $cntry = $_GET["cntry"];
                        $product_id = $_GET["product_id"];
                        try {
                            # Making an API request can throw an exception
                            $product = $shopify('POST /admin/products/' . $product_id . '/metafields.json', array(), array
                                (
                                'metafield' => array
                                    (
                                    "namespace" => "Inventory",
                                    "key" => "country",
                                    "value" => $cntry,
                                    "value_type" => "string",
                                )
                            ));
                            echo '<pre>';
                            print_r($product);
                        } catch (shopify\ApiException $e) {
                            # HTTP status code was >= 400 or response contained the key 'errors'
                            echo $e;
                            print_R($e->getRequest());
                            print_R($e->getResponse());
                        } catch (shopify\CurlException $e) {
                            # cURL error
                            echo $e;
                            print_R($e->getRequest());
                            print_R($e->getResponse());
                        }
                    }
                    ?>
                </div>

                <div class="col-sm-4">
                    <div class="col-sm-offset-1">
                        <div class="sidebar-module sidebar-module-inset">
                            <h4>About</h4>
                            <p>Etiam porta <em>sem malesuada magna</em> mollis euismod. Cras mattis consectetur purus sit amet fermentum. Aenean lacinia bibendum nulla sed consectetur.</p>
                        </div>
                        <div class="sidebar-module">
                            <h4>Elsewhere</h4>
                            <ol class="list-unstyled">
                                <li><a href="#">GitHub</a></li>
                                <li><a href="#">Twitter</a></li>
                                <li><a href="#">Facebook</a></li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
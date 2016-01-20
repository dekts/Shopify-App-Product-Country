<?php

	session_start();
        
	require __DIR__.'/vendor/autoload.php';
	use phpish\shopify;

	require __DIR__.'/conf.php';

	$shopify = shopify\client(SHOPIFY_SHOP, SHOPIFY_APP_API_KEY, SHOPIFY_APP_PASSWORD, true);
        ?>
        <?php
            $countries = $shopify('GET /admin/countries.json', array());
            $products = $shopify('GET /admin/products.json', array('published_status'=>'published'));

            foreach ($products as $value) {
                $id = $value['id'];
                $title = $value['title'];
        ?>
        <form action = "<?php $_PHP_SELF ?>" method = "GET">
            <input type="hidden" name="product_id" value="<?php echo $id ?>" />
            <table>
                <tr>
                    <td width="200px"><label><?php echo $title ?></label></td>
                    <td>
                        <select name="cntry">
                        <?php 
                            foreach ($countries as $value) {
                                $country = $value['name'];
                        ?>
                        <option value="<?php echo $country ?>"><?php echo $country ?></option>
                        <?php } ?>
                        </select>
                    </td>
                    <td><input type = "submit" value="Save"/></td>
                </tr>
            </table>
        </form>
        
        <?php
            }
        if( $_GET["cntry"] && $_GET["product_id"]) {
            $cntry = $_GET["cntry"];
            $product_id = $_GET["product_id"];
            try
            {
                # Making an API request can throw an exception
                $product = $shopify('POST /admin/products/'.$product_id.'/metafields.json', array(), array
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
            }
            catch (shopify\ApiException $e)
            {
                # HTTP status code was >= 400 or response contained the key 'errors'
                echo $e;
                print_R($e->getRequest());
                print_R($e->getResponse());
            }
            catch (shopify\CurlException $e)
            {
                # cURL error
                echo $e;
                print_R($e->getRequest());
                print_R($e->getResponse());
            }
        }
            
?>

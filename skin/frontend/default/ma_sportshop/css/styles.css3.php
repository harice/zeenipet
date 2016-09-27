<?php
    header('Content-type: text/css; charset: UTF-8');
    header('Cache-Control: must-revalidate');
    header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 3600) . ' GMT');
    $url = $_REQUEST['url'];
?>

.block-subscribe .input-box-inner{
    -webkit-border-radius:5px 0 0 5px;
    -moz-border-radius:5px 0 0 5px;
    border-radius:5px 0 0 5px;
    behavior: url(<?php echo $url; ?>css/css3.htc);
}

#nav,
.toolbar,
.product-view .product-shop .product-name,
.block,
.breadcrumbs,
.product-view .product-img-box .product-image,
.category-image, .cart .crosssell, .cart .discount, .cart .shipping, .cart .totals,
.fieldset,
.wine_menu .container,
.products-list li.item,
.top-cart-content,
.ma-fancyproduct li

{
	-moz-box-shadow: 2px 3px 2px 1px #D1D1D1;
	-webkit-box-shadow: 2px 3px 2px 1px #D1D1D1;
	box-shadow: 2px 3px 2px 1px #D1D1D1;
}

.header
{
	-moz-box-shadow: 0 -7px 5px 2px #bfc0c0;
	-webkit-box-shadow: 0 -7px 5px 2px #bfc0c0;
	box-shadow: 0 -7px 5px 2px #bfc0c0;
}

.ma-nav-inner
{
	-moz-box-shadow: 0 0 5px 2px #bfc0c0;
	-webkit-box-shadow: 0 0 5px 2px #bfc0c0;
	box-shadow: 0 0 5px 2px #bfc0c0;
}

.main{
	-moz-box-shadow: 0 6px 5px 2px #bfc0c0;
	-webkit-box-shadow: 0 6px 5px 2px #bfc0c0;
	box-shadow: 0 6px 5px 2px #bfc0c0;
}

.ma-featuredproductslider-container .flexslider .slides .featuredproductslider-item-inner,
.products-grid .item-inner{
	-moz-box-shadow: 2px 3px 2px 1px #d7d7d7;
	-webkit-box-shadow: 2px 3px 2px 1px #d7d7d7;
	box-shadow: 2px 3px 2px 1px #d7d7d7;
}

#back-top
{
    border-radius:18px;
    -moz-border-radius:18px;
    -webkit-border-radius:18px;

    box-shadow:0px 0px 7px rgba(0,0,0,0.8) inset, 0px 0px 0px 4px rgba(255,255,255,0.5);
    -moz-box-shadow:0px 0px 7px rgba(0,0,0,0.8) inset, 0px 0px 0px 4px rgba(255,255,255,0.5);
    -webkit-box-shadow:0px 0px 7px rgba(0,0,0,0.8) inset, 0px 0px 0px 4px rgba(255,255,255,0.5);
	
    transition:all 0.5s ease-in-out;
    -moz-transition:all 0.5s ease-in-out;
    -o-transition:all 0.5s ease-in-out;
    -webkit-transition:all 0.5s ease-in-out;
}
#back-top:hover
{
    box-shadow:0px 0px 4px #aaa inset, 0px 0px 0px 4px #aaa;
    -moz-box-shadow:0px 0px 4px #aaa inset, 0px 0px 0px 4px #aaa;
    -webkit-box-shadow:0px 0px 4px #aaa inset, 0px 0px 0px 4px #aaa;

   -webkit-transition-delay: 0.1s;
   -moz-transition-delay: 0.1s;
   -o-transition-delay: 0.1s;
   -ms-transition-delay: 0.1s;
   transition-delay: 0.1s;
}
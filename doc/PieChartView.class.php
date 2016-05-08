<?php


class PieChartView extends TPage
{
    private $html;
    
    /**
     * Class constructor
     * Creates the page
     */
    function __construct()
    {
        parent::__construct();
        
        $cart = new PPieChart('Progs',true);
        $data = [
        'firefox'=>60,
        'IE'=>10,
        'Google'=>40
        ];
        
        $cart->setData($data);
        
        $cart->setSize(500,500);
        
        parent::add($cart);
    }
}
?>

<?php
class CPCurrencyAll{

  public static function cp_all_currencies_shortcode($atts){
    $html = '';
    
    if (isset($atts['basecurrency']) and $atts['basecurrency']!=''){
      $base_currency = trim(mb_strtoupper($atts['basecurrency']));
    } else {
      $base_currency = 'USD';
    }
    
    if (isset($atts['limit']) and $atts['limit']!=''){
      $limit = (int)$atts['limit'];
    } else {
      $limit = 500;
    }
    
    if (isset($atts['perpage']) and $atts['perpage']!=''){
      $perpage = (int)$atts['perpage'];
    } else {
      $perpage = 100;
    }
    
    
    if (isset($atts['locale']) and $atts['locale']!=''){
      $locale = 'locale="'.$atts['locale'].'"';
    } else {
      $locale = '';
    }

    //load libraries
    CPCommon::cp_load_scripts('datatable');
    CPCommon::cp_load_scripts('lazy');
    
    $html .= '
    <table class="cp-table cp-cryptocurrencies-table"></table>
    <script type="text/javascript">
    //get list of currencies
    var toCurrency = \''.$base_currency.'\';
    var apiUrl = \'https://api.coinmarketcap.com/v1/ticker/?convert=\'+\''.$base_currency.'\'+\'&limit='.$limit.'\';
    console.log(apiUrl);
    jQuery.get( apiUrl, function( data ) {

      //prepare dataset for datatable
      var dataSet = [];
      for (var currentCurrency in data){
        //console.log(data[currentCurrency]);
        var name = data[currentCurrency].name;
        var rank = data[currentCurrency].rank;
        var price_number = data[currentCurrency][\'price_'.mb_strtolower($base_currency).'\'];
        var price = price_number.toLocaleString('.$locale.')+\' \'+toCurrency;
        var supply = parseInt(data[currentCurrency].available_supply).toLocaleString('.$locale.');
        var volume = parseInt(data[currentCurrency][\'24h_volume_'.mb_strtolower($base_currency).'\']+\' \').toLocaleString('.$locale.');
        if (data[currentCurrency].percent_change_24h > 0){
          var changeClass = "change-inc";
        } else {
          var changeClass = "change-dec";
        }
        var change = "<span class=\""+changeClass+"\">"+data[currentCurrency].percent_change_24h+\'%\'+"</span>";
        var marketCap = parseInt(data[currentCurrency][\'market_cap_'.mb_strtolower($base_currency).'\']).toLocaleString('.$locale.');
        var image = "<img class=\"lazy\" data-src=\"'.CP_URL.'images/"+data[currentCurrency].symbol.toLowerCase()+".png\" style=\"max-width:20px;\" />";
        
        dataSet.push([rank, image+\' \'+name, marketCap, price, volume, supply, change]);
      }
      
      //show datatable
      jQuery(".cp-cryptocurrencies-table").DataTable({
        data: dataSet,
        columns: [{ title: "#" },{ title: "Coin" }, { title: "Market Cap, '.$base_currency.'" }, { title: "Price" }, { title: "Volume (24h), '.$base_currency.'" }, { title: "Circulating supply" }, { title: "Change (24h)" }, ],
        "order": [ [0, \'asc\'] ],
        "pageLength": '.$perpage.',
        "lengthMenu": [ [10, 50, 100, 500, 1000, -1], [10, 50, 100, 500, 1000, "All"] ],
        drawCallback: function() {
          var lazy = jQuery(".cp-cryptocurrencies-table img").Lazy({
            chainable: false,
            defaultImage: \'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAABGdBTUEAAK/INwWK6QAAABl0RVh0U29mdHdhcmUAQWRvYmUgSW1hZ2VSZWFkeXHJZTwAAAMjSURBVHjaXFNLaFVXFF3nd+/7RhtJn4lRQ0xbQcQKrfUHqaSoaMDoQBw5Uumg1KGiOCjFOhHspNBCwYETkSKCpY0DbeLARoOKhjQ1mkT88HwvNr68vO+959P9XhINXs7mcA97r73W2ucwvPc1N6qGr7Z07N66oX1bJJFsA+NsOlee6Ls12vfHjeHeisbUwny28Odwz6r9Rw9tOt2yMtahIhLCi4OJCBw4dCjxYGhm9OT3v/1wc3D8IqUHFE7MF3935JNjJ75d80tscbHRjyWgoi0QfgrSa6Z9KZhSaE6FS/bs2twzNvZa/fskfb8GUgc4uDPVc+qb5l+NKCARp2QWI2xa1tJmiSenlQSTTZAig67OT7fe6BvJvZrMj4jUB17ip+NNl+OLS40siINqwOd4MfZOIVmBwmSaQD5EPDGFttb2tZeuDNwT3VsS3Qe69ddhBQjLGsrnEIK9tWcehHE6Vx6CqoA1ZSxflogP/P20Itet5tu40rDFWqKD0z5syMBIAp8VUIeqMbBawFQDck7Bj+Sx/cu1X8i2VraSCYtIQwATGNhgGlZaApjtzOck2BoTq8CZByGjNJVX+HjV0iapnTNcWQjqAEYAxSJcYGdNrBcSCIUjAGMUwhKxNBEacYBkLKbk2AszLj1H1AC/QaNYJgfdDMmYvSaczckgD6oFEkWIBjlIxfFmqhDwvtthf1hvSNdFOXiLNEJLY7MFAslRTJMveZSn/6PueTrPw7kMmI5jYHAkK15mXXrzerX7ow6TCisCkUVkkuMIKhLcki/EpFqy0EEVnLQwTmfCIZdO4OSP1/qFda46NMKye3fIfdGoFbos4cUNRMSiUvQQkrGCFWsm1GVoUULUtuDsz3ce995+3lu/MtkpMz70j9A7O0VnssHySkEQbQ5J0+DUzWjSzWgyKoQXNOLCpeeZ0xeGrlqHm/NvwU680MPX+0WuvcVfs6KVJT2fZsBq19LVJySMRGYihnPn00/OXBz73Tj8RXWDC19jDWwJpW7s+iy2r2uj/3nHcplSjKvsJCvdHQ4m/7wz8+jZ68pDyqs9pHsUmf8FGAD+bVsK2T9HVwAAAABJRU5ErkJggg==\'                            
          });
          lazy.update(); 
        },
      });
    } );
    </script>
    ';

    return $html;
  }
}
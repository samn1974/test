<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

// BEGIN ENQUEUE PARENT ACTION
// AUTO GENERATED - Do not modify or remove comment markers above or below:

if ( !function_exists( 'chld_thm_cfg_locale_css' ) ):
    function chld_thm_cfg_locale_css( $uri ){
        if ( empty( $uri ) && is_rtl() && file_exists( get_template_directory() . '/rtl.css' ) )
            $uri = get_template_directory_uri() . '/rtl.css';
        return $uri;
    }
endif;
add_filter( 'locale_stylesheet_uri', 'chld_thm_cfg_locale_css' );

if ( !function_exists( 'chld_thm_cfg_parent_css' ) ):
    function chld_thm_cfg_parent_css() {
        wp_enqueue_style( 'chld_thm_cfg_parent', trailingslashit( get_template_directory_uri() ) . 'style.css', array(  ) );
    }
endif;
add_action( 'wp_enqueue_scripts', 'chld_thm_cfg_parent_css', 10 );

// END ENQUEUE PARENT ACTION
//Samit Naha-fico slider
function enqueue_custom_script() {
  wp_enqueue_script( 'custom-script', get_stylesheet_directory_uri() . '/custom.js', array( 'jquery' ), '1.0', true );
}
add_action( 'wp_enqueue_scripts', 'enqueue_custom_script' );

//ratesheet
add_action('init', 'register_custom_functions');
function register_custom_functions() {
    function pmt($a, $r, $n) {
        $r=$r/100;
        $r=$r/12;
        $denominator = 1 - pow(1 + $r, -$n);
        return round($a * $r / $denominator, 2);
    }

    function total_interest($principal, $interest, $number_of_payments, $monthly_payment) {
        $total_interest = ($monthly_payment * $number_of_payments) - $principal;
        return $total_interest;
    }

    function RATE($A, $P, $N, $i) {
        $i=($i/100)/12;
        for ($x=0; $x<=30; $x++) {
            $inew = $i - ($P - $P * pow(1 + $i, -$N) - $A * $i) / (360 * $P * pow(1 + $i, -($N + 1)) - $A);
            $i = $inew;
        }

        $interest_rate = $i * 12 * 100;
        return round($interest_rate, 3);
    }
}


add_action('wp_ajax_get_ratesheet', 'get_ratesheet_callback');
add_action('wp_ajax_nopriv_get_ratesheet', 'get_ratesheet_callback');

function get_ratesheet_callback() {
  global $wpdb;
  $loanAmount = floatval($_POST['loanAmount']);
  $pValue =   floatval($_POST['propertyValue']);
  $county =  $_POST["county"];
  $occupancy = $_POST["occupancy"];
    $loan_purpose = $_POST["purpose"];
    $loan_program = $_POST["term"];
    $property_type = $_POST["structure"];
    $fico_credit = floatval($_POST["fico"]);
    $impound = $_POST["impound"];
    $income = $_POST["income"];
    $secloan = $_POST["subfin"];
    $secLoanAmt = floatval($_POST['secLoanAmt']);

  $pLoan = $loanAmount;
    $vSubFinance="0";
    $vNOIMPOUND="0" ;
    $govConfLimit = 726200;
    $mycomp = 0.5;
    $maxloanamount="'3000000'";
    $state="CA";
    $maxlimit = 726200;
    $adminfee=1500;
    $recordingfee=0;
    $underwriting=1195;
    $processing=1050;
    $mirate=0;    
    $outercond = "(1=1 ";
    $condition = "(1=1 ";
    $term="0";
    $pmi=0;
    $subfinamt = 0;
    if ($secLoanAmt > 0) {
        $subfinamt = $secLoanAmt;
    }
    $ltv = number_format(($loanamount/$propertyvalue * 100), 2, '.', '');
    $cltv = $ltv;
    $closingfee= $loanamount*.25/100;
    $vFicoUpperLimit=0;
    if($secloan == "Yes")
    {
        $cltv = number_format((($loanamount + $subfinamt)/$propertyvalue * 100), 2, '.', '');
        $vSubFinance="1";
    }
    if ($impound == "No"){
        $vNOIMPOUND="1" ;
    }
    if ($fico_credit >= 620 && $fico_credit <= 639) {
        $vFicoUpperLimit=639;
        $mirate=1;
        
    }
    if ($fico_credit >= 640 && $fico_credit <= 659) {
        $vFicoUpperLimit=659;
        $mirate=1;
    }
    if ($fico_credit >= 660 && $fico_credit <= 679) {
        $vFicoUpperLimit=679;
        $mirate=1;
    }
    if ($fico_credit >= 680 && $fico_credit <= 699) {
        $vFicoUpperLimit=699;
        $mirate=.7;
    }
    if ($fico_credit >= 700 && $fico_credit <= 719) {
        $vFicoUpperLimit=719;
        $mirate=.6;
    }
    if ($fico_credit >= 720 && $fico_credit <= 739) {
        $vFicoUpperLimit=739;
        $mirate=.5;
    }
    if ($fico_credit >= 740 && $fico_credit <= 759) {
        $vFicoUpperLimit=759;
        $mirate=.4;
    }
    if ($fico_credit >= 760 && $fico_credit <= 779) {
        $vFicoUpperLimit=779;
        $mirate=.3;
    }
    if ($fico_credit >= 780 && $fico_credit < 799) {
        $vFicoUpperLimit=799;
        $mirate=.3;
    }
    if ($fico_credit >= 800) {
        $vFicoUpperLimit=810;
        $mirate=.3;
    }
    $mi=(($loanamount*$mirate)/100)/12;

    if ($property_type == "Single Family" || $property_type == "Condo")
    {
        $colname = "unit1";
    } elseif ($property_type == "Duplex")
    {
        $colname = "unit2";
    } elseif ($property_type == "Triplex")
    {
        $colname = "unit3";
    } elseif ($property_type == "Fourplex")
    {
        $colname = "unit4";
    }
    $county_data = $wpdb->get_row(
        $wpdb->prepare("SELECT %s as maxlimit, IsHighBal FROM ".$table_prefix."lg_county WHERE county = %s", $colname, $county)
    );
    
    $maxlimit = $county_data->maxlimit;
    $isHighBalance = $county_data->IsHighBal;

    
if ($isHighBalance == 1 && $pLoan > $govConfLimit && $pLoan <=$maxlimit )
{
    $category = 'Super Conforming';
    $condition .= " and ae.ConformingHighBalance = 1";
    $outercond .= " and IsHB=1";
}elseif ($pLoan > $maxlimit)
{
    $category = 'Jumbo';
    $condition .= " and ae.Jumbo = 1";
    $outercond .= " and IsJumbo=1";
}else{
    $category = 'Conforming';
    $condition .= " and ae.Conforming = 1";
    $outercond .= " and IsConf=1";
}
$outercond .= " )";
if ($occupancy == "Primary Residence"){
    $condition .= " and ae.PrimaryHome = 1";
}elseif ($occupancy == "Second Home"){
    $condition .= " and ae.SecondHome = 1";
}elseif ($occupancy == "Investment Property"){
    $condition .= " and ae.RentalProperty = 1";
}

if ($loan_purpose == "Purchase"){
    $condition .= " and ae.Purchase = 1";
}elseif ($loan_purpose == "Refinance"){
    $condition .= " and ae.Refinance = 1";
}elseif ($loan_purpose == "Cash Out"){
    $condition .= " and ae.CashOut = 1";
}

if ($loan_program == "30 Years Fixed"){
    $condition .= " and ae.30YrFixed = 1";
    $term="30";
    $nper=30*12;
}elseif ($loan_program == "20 Years Fixed"){
    $condition .= " and ae.20YrFixed = 1";
    $term="20";
    $nper=20*12;
}elseif ($loan_program == "15 Years Fixed"){
    $condition .= " and ae.15YrFixed = 1";
    $term="15";
    $nper=15*12;
}elseif ($loan_program == "10 Years Fixed"){
    $condition .= " and ae.10YrFixed = 1";
    $term="10";
    $nper=10*12;
}elseif ($loan_program == "5 ARM"){
    $condition .= " and ae.5ARM = 1";
    $term="5 ARM";
    $nper=30*12;
}
elseif ($loan_program == "7 ARM"){
    $condition .= " and ae.7ARM = 1";
    $term="7 ARM";
    $nper=30*12;
}
elseif ($loan_program == "3 ARM"){
    $condition .= " and ae.3ARM = 1";
    $term="3 ARM";
    $nper=30*12;
}


if ($property_type == "Single Family"){
    $condition .= " and ae.SingleFamily = 1";
}elseif ($property_type == "Condo"){
    $condition .= " and ae.Condo = 1";
}elseif ($property_type == "Duplex"){
    $condition .= " and ae.2Units = 1";
}elseif ($property_type == "Triplex"){
    $condition .= " and ae.3Units = 1";
}elseif ($property_type == "Fourplex"){
    $condition .= " and ae.4Units = 1";
}

if ($income == "Salaried W2"){
    $condition .= " and ae.SalariedW2 = 1";
}elseif ($income == "Self Employed"){
    $condition .= " and ae.SelfEmployed = 1";
}elseif ($income == "Other"){
    $condition .= " and ae.OtherIncome = 1";
}

if ($impound == "Yes"){
    $condition .= " and ae.Impound = 1";
}elseif ($impound == "No"){
    $condition .= " and ae.NoImpound = 1";
}
if ($secloan == "Yes"){
    $condition .= " and ae.SubFinanceYes = 1";
}elseif ($secloan == "No"){
    $condition .= " and ae.SubFinanceNo = 1";
}

$condition .= " )";

$sql = "CREATE TEMPORARY TABLE " . $table_prefix . "temp_lg_pricelist AS (
        SELECT r.LenderId, r.PriceDate, r.Term, r.Rate, (r.Baseprice * -1) + 100 - p.pts - 0.50 as price, p.pts, r.LenderProduct
        FROM " . $table_prefix . "lg_ratesheet r,
             (SELECT adj.LenderID, adj.product as Product, SUM(adj.points) as pts
              FROM (SELECT a.*,
                           IF(a.useFico=1,IF(a.minFico<=? AND a.maxFico>=?,'FICOGOOD','FICOBAD'),'FICOGOOD') AS Fico,
                           IF(a.useLTV=1,IF(a.minLTV<=? AND a.maxLTV>=?,'LTVGOOD','LTVBAD'),'LTVGOOD') AS Ltv,
                           IF(a.useCLTV=1,IF(a.minCLTV<=? AND a.maxCLTV>=?,'CLTVGOOD','CLTVBAD'),'CLTVGOOD') AS CLtv,
                           IF(a.useLoanAmt=1,IF(a.minLoanAmt<=? AND a.maxLoanAmt>=?,'LOANAMTGOOD','LOANAMTBAD'),'LOANAMTGOOD') AS LoanAmt
                    FROM " . $table_prefix . "lg_adjustment AS a, " . $table_prefix . "lg_adjEligibility AS ae
                    WHERE a.AdjustmentCode = ae.AdjustmentCode AND a.LenderID=ae.LenderID AND " . $condition . "
                   ) AS adj
              WHERE adj.Fico='FICOGOOD' AND adj.Ltv='LTVGOOD' AND adj.Cltv='CLTVGOOD' AND adj.LoanAmt='LOANAMTGOOD' 
              GROUP BY adj.product, adj.LenderID
             ) p
        WHERE r.LenderProduct LIKE p.product AND r.LenderID=p.LenderID AND " . $outercond . " AND ? >= r.MinLoanAmt AND ? > r.minFico AND ? <= r.maxLTV AND r.term=?
      );";

$wpdb->query($wpdb->prepare($sql, $vFicoUpperLimit, $vFicoUpperLimit, $ltv, $ltv, $cltv, $cltv, $loanamount, $loanamount, $vFicoUpperLimit, $ltv, $term));


$sql = "CREATE TEMPORARY TABLE ".$table_prefix."temp_lg_pricelist1 AS
(SELECT LenderId, PriceDate, Term, Rate, pts, MAX(price) AS price, LenderProduct
FROM (
SELECT LenderId, PriceDate, Term, Rate, price, pts, LenderProduct,
ROW_NUMBER() OVER (PARTITION BY Rate ORDER BY price DESC) AS row_num
FROM ".$table_prefix."temp_lg_pricelist
) tmp
WHERE row_num = 1 and price >= 100
GROUP BY Rate, LenderId, PriceDate, Term, pts, LenderProduct) ORDER BY price LIMIT 6;";

$wpdb->query($wpdb->prepare($sql));

$sql="insert into {$table_prefix}temp_lg_pricelist1 (SELECT LenderId, PriceDate, Term, Rate, pts, MAX(price) AS price, LenderProduct
FROM (
SELECT LenderId, PriceDate, Term, Rate, price, pts, LenderProduct,
ROW_NUMBER() OVER (PARTITION BY Rate ORDER BY price DESC) AS row_num
FROM {$table_prefix}temp_lg_pricelist
) tmp
WHERE row_num = 1 and price < 100
GROUP BY Rate, LenderId, PriceDate, Term, pts, LenderProduct) order by price desc limit 5;";
$wpdb->query($wpdb->prepare($sql));


$result = $wpdb->get_results("SELECT LenderId, PriceDate, Term, Rate, price,pts, LenderProduct FROM {$table_prefix}temp_lg_pricelist1 order by price;") ;
$data = array();
foreach ($result as $row) {
  $lender_id = $row->LenderId;
  $price_date = $row->PriceDate;
  $term = $row->Term;
  $rate = $row->Rate;
  $base_price = $row->price;
  $points = $row->pts;
  $lender_product = $row->LenderProduct;
  $displayPoints = $points - 100;
  $discountfee=-1*((100-$points)*$loanamount)/100;
  $totalfee=-1*$discountfee+$adminfee+$underwriting+$processing+$closingfee+$recordingfee;
  $pmt = pmt($loanamount,$row['Rate'], $nper);
  if ($ltv > 80 && $category != 'Jumbo') $newpmt=pmt($loanamount+$totalfee+($mi*120), $rate, $nper);
  else $newpmt=pmt($loanamount+$totalfee, $rate, $nper);
  $apr=RATE($loanamount,$newpmt,$nper,$rate);
  if ($ltv > 80 && $category != 'Jumbo') $pmi=$mi; else $pmi=0;

  $row_data = array(
    'Rate' => $rate,
    'APR' => $apr,
    'Points1' => $displayPoints,
    'Points2' => $discountfee,
    'Costs' => $totalfee,
    'Payment' => $pmt,
    'PMI' => $pmi
  );
  array_push($data, $row_data);
}

wp_send_json($data);}

function enqueue_slick_js() {
  wp_enqueue_script('jquery');
  wp_enqueue_script('slick', 'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js', array('jquery'), '1.8.1', true);
  wp_enqueue_script('handlebars', 'https://cdnjs.cloudflare.com/ajax/libs/handlebars.js/4.7.7/handlebars.min.js', array('jquery'), '4.7.7', true);
}
add_action('wp_enqueue_scripts', 'enqueue_slick_js');

function enqueue_slick_css() {
  wp_enqueue_style('slick-css', 'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css', array(), '1.8.1', 'all');
  wp_enqueue_style('slick-theme-css', 'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css', array(), '1.8.1', 'all');
}
add_action('wp_enqueue_scripts', 'enqueue_slick_css');


add_action('wp_footer', 'my_script_init');
function my_script_init() {
?>
<script id="my-slider-template" type="text/x-handlebars-template">
  <div class="slider-container">
    <div class="my-slider">
      {{#each this}}
      <div class="my-slide">
                <div class="htable">
                    <h5>
                        <div class="hrow">
                            <div class="hcell"> 
                                <div class="hlabel"> Rate </div>
                            </div>
                            <div class="hcell"> 
                                <div class="hvalue"> {{Rate}} </div>
                            </div>
                        </div>
                        <div class="hrow">
                            <div class="hcell"> 
                                <div class="hlabel"> APR </div>
                            </div>
                            <div class="hcell"> 
                                <div class="hvalue">  {{APR}} </div>
                            </div>
                        </div>
                        <div class="hrow">
                            <div class="hcell"> 
                                <div class="hlabel"> Points(%) </div>
                            </div>
                            <div class="hcell"> 
                                <div class="hvalue">  {{Points1}} </div>
                            </div>
                        </div>
                        <div class="hrow">
                            <div class="hcell"> 
                                <div class="hlabel"> Points($) </div>
                            </div>
                            <div class="hcell"> 
                                <div class="hvalue">  {{Points2}} </div>
                            </div>
                        </div>
                        <div class="hrow">
                            <div class="hcell"> 
                                <div class="hlabel"> Closing Costs </div>
                            </div>
                            <div class="hcell"> 
                                <div class="hvalue">  {{Costs}} </div>
                            </div>
                        </div>
                        <div class="hrow">
                            <div class="hcell"> 
                                <div class="hlabel"> Payment(P+I) </div>
                            </div>
                            <div class="hcell"> 
                                <div class="hvalue">  {{Payment}} </div>
                            </div>
                        </div>
                        <div class="hrow">
                            <div class="hcell"> 
                                <div class="hlabel"> PMI </div>
                            </div>
                            <div class="hcell"> 
                                <div class="hvalue">  {{PMI}} </div>
                            </div>
                        </div>
                    </h5>       
         </div>
      </div>
      {{/each}}
    </div>
  </div>
</script>

<script type="text/javascript">
 jQuery(document).ready(function($) {
  function generateSlider(loanAmount,propertyValue, county, occupancy, purpose, term, structure, fico, impound, income, subfin, secLoanAmt) {
    $.ajax({
      url: '<?php echo admin_url('admin-ajax.php'); ?>',
      type: 'POST',
      data: { action: 'get_ratesheet', loanAmount: loanAmount,   propertyValue: propertyValue,
      county: county,
      occupancy: occupancy,
      purpose: purpose,
      term: term,
      structure: structure,
      fico: fico,
      impound: impound, 
      income: income, 
      subfin: subfin,
      secLoanAmt: secLoanAmt },
      success: function(results) {
        // Check whether there are any results
if (results.length > 0) {
  // Render the template with the response data
  var template = Handlebars.compile($('#my-slider-template').html());
  $('.my-slider-container').html(template(results));

  // Initialize Slick slider
  $('.my-slider').slick({
    dots: true,
    infinite: true,
    speed: 300,
    slidesToShow: 1,
    slidesToScroll: 1,
    centerMode:false,
    centerPadding:'50px',
    adaptiveHeight:true,
    useCSS:true,
    prevArrow:'<button type="button" data-role="none" class="slick-prev"></button>',
    nextArrow:'<button type="button" data-role="none" class="slick-next"></button>'	
  });

  // Set color of hlabel and hvalue to black after the slider is initialized
  $('.my-slider .hlabel, .my-slider .hvalue').css('color', '#000000');

} else {
  $('.my-slider-container').html('<p>No results found.</p>');
}
      },
      error: function(jqXHR, textStatus, errorThrown) {
        console.log('Error: ' + errorThrown);
        console.log('Status: ' + textStatus);
        console.dir(jqXHR);
      }
    });
  }

  // Read the default loan amount value from the input field
  var defaultLoanAmount = 400000;
  var defaultPropValue = 500000;
  var defaultCounty = "San Joaquin";
  var defaultOccupancy = "Primary Home";
  var defaultPurpose = "Purchase";
  var defaultStructure = "Single Family";
  var defaultTerm = "30 Years Fixed";
  var defaultFICO = 740;
  var defaultIncome = "Salaried W2";
  var defaultImpound = "No";
  var defaultSubFin = "No";
  var defaultSubFinAmt = 0;
  var defaultLTV = (defaultLoanAmount / defaultPropValue * 100).toFixed(2) + '%';
  var defaultCLTV = ((defaultLoanAmount + defaultSubFinAmt) / defaultPropValue * 100).toFixed(2) + '%';


   $('.loan-amount-widget .lval').text(defaultLoanAmount.toLocaleString('en-US', {style: 'currency', currency: 'USD'}));
   $('.loan-amount-widget .pval').text(defaultPropValue.toLocaleString('en-US', {style: 'currency', currency: 'USD'}) );
   $('.loan-amount-widget .county').text(defaultCounty );
   $('.loan-amount-widget .occupancy').text(defaultOccupancy );
   $('.loan-amount-widget .purpose').text(defaultPurpose );
   $('.loan-amount-widget .structure').text(defaultStructure );
   $('.loan-amount-widget .term').text(defaultTerm );
   $('.loan-amount-widget .fico').text(defaultFICO );
   $('.loan-amount-widget .income').text(defaultIncome );
   $('.loan-amount-widget .impound').text(defaultImpound );
   $('.loan-amount-widget .subfin').text(defaultSubFin );
   $('.loan-amount-widget .secloanval').text(defaultSubFinAmt.toLocaleString('en-US', {style: 'currency', currency: 'USD'}) );
   $('.loan-amount-widget .ltv').text(defaultLTV );
   $('.loan-amount-widget .cltv').text(defaultCLTV ); 

  // Generate the slider on page load
  generateSlider(
	defaultLoanAmount,
	defaultPropValue,
 	defaultCounty,
 	defaultOccupancy,
 	defaultPurpose ,
	defaultTerm ,
	defaultStructure,
	defaultFICO,
	defaultImpound ,
  	defaultIncome ,
	defaultSubFin ,
	defaultSubFinAmt 
	);

  // Attach click event to "Get Rate" button
  $('.get-rate-button').on('click', function(event) {
    event.preventDefault();
    var loanAmount = parseFloat($('#pLoan').val());
  var PropValue = parseFloat($('#pValue').val());
  var County = $('#county').val();
  var Occupancy = $('#occupancy').val();
  var Purpose = $('#loan_purpose').val();
  var Structure = $('#property_type').val();
  var Term = $('#loan_program').val();
  var FICO = $('#fico_credit').val();
  var Income = $('#income').val();
  var Impound = $('#impound').is(':checked') == true ? 'Yes' : 'No';
  var SubFin = $('#sub-fin').is(':checked') == true ? 'Yes' : 'No';
  var SubFinAmt = parseFloat($('#secLoanAmt').val());
  var LTV = (loanAmount / PropValue * 100).toFixed(2) + '%';
  var CLTV = ((loanAmount + SubFinAmt) / PropValue * 100).toFixed(2) + '%';

   $('.loan-amount-widget .lval').text(loanAmount.toLocaleString('en-US', {style: 'currency', currency: 'USD'}));
   $('.loan-amount-widget .pval').text(PropValue.toLocaleString('en-US', {style: 'currency', currency: 'USD'}));
   $('.loan-amount-widget .county').text(County );
   $('.loan-amount-widget .occupancy').text(Occupancy );
   $('.loan-amount-widget .purpose').text(Purpose );
   $('.loan-amount-widget .structure').text(Structure );
   $('.loan-amount-widget .term').text(Term );
   $('.loan-amount-widget .fico').text(FICO );
   $('.loan-amount-widget .income').text(Income );
   $('.loan-amount-widget .impound').text(Impound );
   $('.loan-amount-widget .subfin').text(SubFin );
   $('.loan-amount-widget .secloanval').text(SubFinAmt.toLocaleString('en-US', {style: 'currency', currency: 'USD'}) );
   $('.loan-amount-widget .ltv').text(LTV );
   $('.loan-amount-widget .cltv').text(CLTV ); 

    generateSlider(
	loanAmount,
	PropValue,
	County,
	Occupancy,
	Purpose,
	Term ,
  	Structure,
	FICO,
	Impound ,
	Income ,
	SubFin ,
	SubFinAmt 
  	);
  });
});

</script>
<?php
}

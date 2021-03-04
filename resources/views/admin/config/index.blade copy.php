@extends('admin.layouts.app')

@section('title', 'Umum')
@push('breadcrump')
    <li class="active">Umum</li>
@endpush
@section('content')
<div class="row">
    <div class="col-lg-12">
    <div class="box box-danger">
        <div class="box-header">
          <h3 class="box-title">Silahkan perbarui informasi di bawah ini.</h3>
          <!-- tools box -->
          <div class="pull-right box-tools">
            <button form="form" type="submit" class="btn btn-sm btn-danger" title="Simpan"><i class="fa fa-save"></i></button>
          </div>
          <!-- /. tools -->
        </div>
        <div class="box-body">
          <form action="https://spos.tecdiary.net/settings" class="validation" enctype="multipart/form-data" method="post" accept-charset="utf-8">
            <input type="hidden" name="spos_token" value="1936333b65223188edaef91facbabd15" />
            <div class="row">
              <div class="col-md-6">
                  <div class="form-group">
                    <label for="site_name">Site name</label>                                    <input type="text" name="site_name" value="SimplePOS"  class="form-control" id="site_name" required="required" />
                  </div>
                  <div class="form-group">
                    <label for="tel">Tel</label>                                    <input type="text" name="tel" value="0105292122"  class="form-control" id="tel" required="required" />
                  </div>
                  <div class="form-group">
                    <label for="language">Language</label>                                                                        
                    <select name="language" class="form-control tip select2" id="language" required="required" style="width:100%;">
                        <option value="arabic">Arabic</option>
                        <option value="english" selected="selected">English</option>
                        <option value="indonesian">Indonesian</option>
                        <option value="portuguese-brazilian">Portuguese (Brazil)</option>
                    </select>
                  </div>
                  <div class="form-group">
                    <label for="theme">Theme</label>                                                                        
                    <select name="theme" class="form-control tip select2" id="theme" required="required" style="width:100%;">
                        <option value="default" selected="selected">Default</option>
                    </select>
                  </div>
                  <div class="form-group">
                    <label for="theme_style">Theme Style</label>                                                                        
                    <select name="theme_style" class="form-control tip select2" id="theme_style" required="required" style="width:100%;">
                        <option value="black">Black</option>
                        <option value="black-light">Black Light</option>
                        <option value="blue">Blue</option>
                        <option value="blue-light">Blue Light</option>
                        <option value="green" selected="selected">Green</option>
                        <option value="green-light">Green Light</option>
                        <option value="purple">Purple</option>
                        <option value="purple-light">Purple Light</option>
                        <option value="red">Red</option>
                        <option value="red-light">Red Light</option>
                        <option value="yellow">Yellow</option>
                        <option value="yellow-light">Yellow Light</option>
                    </select>
                  </div>
                  <div class="form-group">
                    <label for="overselling">Overselling</label>                                                                        
                    <select name="overselling" class="form-control select2" id="overselling" required="required" style="width:100%;">
                        <option value="0">Disable</option>
                        <option value="1" selected="selected">Enable</option>
                    </select>
                  </div>
                  <div class="form-group">
                    <label for="multi_store">Multiple Stores</label>                                                                        
                    <select name="multi_store" class="form-control select2" id="multi_store" required="required" style="width:100%;">
                        <option value="0" selected="selected">Disable</option>
                        <option value="1">Enable</option>
                    </select>
                  </div>
                  <div class="form-group">
                    <label for="currency_code">Currency code</label>                                    <input type="text" name="currency_prefix" value="USD"  class="form-control" id="currency_code" required="required" />
                  </div>
                  <div class="form-group">
                    <label for="auto_print">Auto Print Receipt</label>                                    
                    <select name="auto_print" class="form-control select2" id="auto_print" required="required" style="width:100%;">
                        <option value="0" selected="selected">Disable</option>
                        <option value="1">Enable</option>
                    </select>
                  </div>
                  <div class="form-group">
                    <label for="after_sale_page">After Sale Page</label>                                                                        
                    <select name="after_sale_page" class="form-control select2" id="after_sale_page" required="required" style="width:100%;">
                        <option value="0" selected="selected">Receipt</option>
                        <option value="1">POS</option>
                    </select>
                  </div>
                  <div class="form-group">
                    <label for="default_discount">Default Discount</label>                                    <input type="text" name="default_discount" value="0"  class="form-control" id="default_discount" required="required" />
                  </div>
                  <div class="form-group">
                    <label for="default_tax_rate">Default Order Tax</label>                                    <input type="text" name="tax_rate" value="5%"  class="form-control" id="default_tax_rate" required="required" />
                  </div>
                  <div class="form-group">
                    <label for="rows_per_page">Row per page</label>                                    
                    <select name="rows_per_page" class="form-control select2" id="rows_per_page" style="width:100%;" required="required">
                        <option value="10" selected="selected">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                  </div>
              </div>
              <div class="col-md-6">
                  <div class="form-group">
                    <label for="pin_code">Pin Code</label>                                    <input type="password" name="pin_code" value="2122"  class="form-control" pattern="[0-9]{4,8}"id="pin_code" />
                  </div>
                  <div class="form-group">
                    <label for="rounding">Rounding</label>                                    
                    <select name="rounding" class="form-control select2" id="rounding" required="required" style="width:100%;">
                        <option value="0">Disable</option>
                        <option value="1" selected="selected">Round to nearest 0.05</option>
                        <option value="2">Round to nearest 0.50</option>
                        <option value="3">Round to nearest number (Integer)</option>
                        <option value="4">Round to next number (Integer)</option>
                    </select>
                  </div>
                  <div class="form-group">
                    <label for="display_product">Display Product</label>                                    
                    <select name="display_product" class="form-control select2" id="display_product" style="width:100%;" required="required">
                        <option value="1">Name</option>
                        <option value="2">Photo</option>
                        <option value="3" selected="selected">Both</option>
                    </select>
                  </div>
                  <div class="form-group">
                    <label for="pro_limit">Product Display Limit</label>                                    <input type="text" name="pro_limit" value="10"  class="form-control" id="pro_limit" required="required" />
                  </div>
                  <div class="form-group">
                    <label for="display_kb">Display Keyboard</label>                                    
                    <select name="display_kb" class="form-control select2" id="display_kb" style="width:100%;" required="required">
                        <option value="1">Yes</option>
                        <option value="0" selected="selected">No</option>
                    </select>
                  </div>
                  <div class="form-group">
                    <label for="item_addition">Item Addition</label>                                    
                    <select name="item_addition" id="item_addition" class="form-control tip select2" required="required" style="width:100%;">
                        <option value="0">Add new item</option>
                        <option value="1" selected="selected">Increase the quantity of item is exists</option>
                    </select>
                  </div>
                  <div class="form-group">
                    <label for="default_category">Default Category</label>                                    
                    <select name="default_category" class="form-control select2" style="width:100%;" id="default_category">
                        <option value="0">Select Default Category</option>
                        <option value="2">Morrocan Path</option>
                        <option value="1" selected="selected">General</option>
                    </select>
                  </div>
                  <div class="form-group">
                    <label for="default_customer">Default Customer</label>                                    
                    <select name="default_customer" class="form-control select2" style="width:100%;" id="default_customer" required="required">
                        <option value="1" selected="selected">Walk-in Client</option>
                        <option value="2">mahmood</option>
                    </select>
                  </div>
                  <div class="form-group">
                    <label for="timezone">Timezone</label> <!-- <a href="http://php.net/manual/en/timezones.php" target="_blank"><i class="fa fa-external-link"></i></a> -->
                    <select name="timezone" class="form-control select2" style="width:100%;" id="timezone" required="required">
                        <option value="">Please select</option>
                        <option value="Pacific/Midway">(GMT-11:00) Midway Island</option>
                        <option value="US/Samoa">(GMT-11:00) Samoa</option>
                        <option value="US/Hawaii">(GMT-10:00) Hawaii</option>
                        <option value="US/Alaska">(GMT-09:00) Alaska</option>
                        <option value="US/Pacific">(GMT-08:00) Pacific Time (US &amp; Canada)</option>
                        <option value="America/Tijuana">(GMT-08:00) Tijuana</option>
                        <option value="US/Arizona">(GMT-07:00) Arizona</option>
                        <option value="US/Mountain">(GMT-07:00) Mountain Time (US &amp; Canada)</option>
                        <option value="America/Chihuahua">(GMT-07:00) Chihuahua</option>
                        <option value="America/Mazatlan">(GMT-07:00) Mazatlan</option>
                        <option value="America/Mexico_City">(GMT-06:00) Mexico City</option>
                        <option value="America/Monterrey">(GMT-06:00) Monterrey</option>
                        <option value="Canada/Saskatchewan">(GMT-06:00) Saskatchewan</option>
                        <option value="US/Central">(GMT-06:00) Central Time (US &amp; Canada)</option>
                        <option value="US/Eastern">(GMT-05:00) Eastern Time (US &amp; Canada)</option>
                        <option value="US/East-Indiana">(GMT-05:00) Indiana (East)</option>
                        <option value="America/Bogota">(GMT-05:00) Bogota</option>
                        <option value="America/Lima">(GMT-05:00) Lima</option>
                        <option value="America/Caracas">(GMT-04:30) Caracas</option>
                        <option value="Canada/Atlantic">(GMT-04:00) Atlantic Time (Canada)</option>
                        <option value="America/La_Paz">(GMT-04:00) La Paz</option>
                        <option value="America/Santiago">(GMT-04:00) Santiago</option>
                        <option value="Canada/Newfoundland">(GMT-03:30) Newfoundland</option>
                        <option value="America/Buenos_Aires">(GMT-03:00) Buenos Aires</option>
                        <option value="Greenland">(GMT-03:00) Greenland</option>
                        <option value="Atlantic/Stanley">(GMT-02:00) Stanley</option>
                        <option value="Atlantic/Azores">(GMT-01:00) Azores</option>
                        <option value="Atlantic/Cape_Verde">(GMT-01:00) Cape Verde Is.</option>
                        <option value="Africa/Casablanca">(GMT) Casablanca</option>
                        <option value="Europe/Dublin">(GMT) Dublin</option>
                        <option value="Europe/Lisbon">(GMT) Lisbon</option>
                        <option value="Europe/London">(GMT) London</option>
                        <option value="Africa/Monrovia">(GMT) Monrovia</option>
                        <option value="Europe/Amsterdam">(GMT+01:00) Amsterdam</option>
                        <option value="Europe/Belgrade">(GMT+01:00) Belgrade</option>
                        <option value="Europe/Berlin">(GMT+01:00) Berlin</option>
                        <option value="Europe/Bratislava">(GMT+01:00) Bratislava</option>
                        <option value="Europe/Brussels">(GMT+01:00) Brussels</option>
                        <option value="Europe/Budapest">(GMT+01:00) Budapest</option>
                        <option value="Europe/Copenhagen">(GMT+01:00) Copenhagen</option>
                        <option value="Europe/Ljubljana">(GMT+01:00) Ljubljana</option>
                        <option value="Europe/Madrid">(GMT+01:00) Madrid</option>
                        <option value="Europe/Paris">(GMT+01:00) Paris</option>
                        <option value="Europe/Prague">(GMT+01:00) Prague</option>
                        <option value="Europe/Rome">(GMT+01:00) Rome</option>
                        <option value="Europe/Sarajevo">(GMT+01:00) Sarajevo</option>
                        <option value="Europe/Skopje">(GMT+01:00) Skopje</option>
                        <option value="Europe/Stockholm">(GMT+01:00) Stockholm</option>
                        <option value="Europe/Vienna">(GMT+01:00) Vienna</option>
                        <option value="Europe/Warsaw">(GMT+01:00) Warsaw</option>
                        <option value="Europe/Zagreb">(GMT+01:00) Zagreb</option>
                        <option value="Europe/Athens">(GMT+02:00) Athens</option>
                        <option value="Europe/Bucharest">(GMT+02:00) Bucharest</option>
                        <option value="Africa/Cairo">(GMT+02:00) Cairo</option>
                        <option value="Africa/Harare">(GMT+02:00) Harare</option>
                        <option value="Europe/Helsinki">(GMT+02:00) Helsinki</option>
                        <option value="Europe/Istanbul">(GMT+02:00) Istanbul</option>
                        <option value="Asia/Jerusalem">(GMT+02:00) Jerusalem</option>
                        <option value="Europe/Kiev">(GMT+02:00) Kyiv</option>
                        <option value="Europe/Minsk">(GMT+02:00) Minsk</option>
                        <option value="Europe/Riga">(GMT+02:00) Riga</option>
                        <option value="Europe/Sofia">(GMT+02:00) Sofia</option>
                        <option value="Europe/Tallinn">(GMT+02:00) Tallinn</option>
                        <option value="Europe/Vilnius">(GMT+02:00) Vilnius</option>
                        <option value="Asia/Baghdad">(GMT+03:00) Baghdad</option>
                        <option value="Asia/Kuwait">(GMT+03:00) Kuwait</option>
                        <option value="Africa/Nairobi">(GMT+03:00) Nairobi</option>
                        <option value="Asia/Riyadh">(GMT+03:00) Riyadh</option>
                        <option value="Asia/Tehran">(GMT+03:30) Tehran</option>
                        <option value="Europe/Moscow">(GMT+04:00) Moscow</option>
                        <option value="Asia/Baku">(GMT+04:00) Baku</option>
                        <option value="Europe/Volgograd">(GMT+04:00) Volgograd</option>
                        <option value="Asia/Muscat">(GMT+04:00) Muscat</option>
                        <option value="Asia/Tbilisi">(GMT+04:00) Tbilisi</option>
                        <option value="Asia/Yerevan">(GMT+04:00) Yerevan</option>
                        <option value="Asia/Kabul">(GMT+04:30) Kabul</option>
                        <option value="Asia/Karachi">(GMT+05:00) Karachi</option>
                        <option value="Asia/Tashkent">(GMT+05:00) Tashkent</option>
                        <option value="Asia/Kolkata">(GMT+05:30) Kolkata</option>
                        <option value="Asia/Kathmandu">(GMT+05:45) Kathmandu</option>
                        <option value="Asia/Yekaterinburg">(GMT+06:00) Ekaterinburg</option>
                        <option value="Asia/Almaty">(GMT+06:00) Almaty</option>
                        <option value="Asia/Dhaka">(GMT+06:00) Dhaka</option>
                        <option value="Asia/Novosibirsk">(GMT+07:00) Novosibirsk</option>
                        <option value="Asia/Bangkok">(GMT+07:00) Bangkok</option>
                        <option value="Asia/Jakarta">(GMT+07:00) Jakarta</option>
                        <option value="Asia/Krasnoyarsk">(GMT+08:00) Krasnoyarsk</option>
                        <option value="Asia/Chongqing">(GMT+08:00) Chongqing</option>
                        <option value="Asia/Hong_Kong">(GMT+08:00) Hong Kong</option>
                        <option value="Asia/Kuala_Lumpur" selected="selected">(GMT+08:00) Kuala Lumpur</option>
                        <option value="Australia/Perth">(GMT+08:00) Perth</option>
                        <option value="Asia/Singapore">(GMT+08:00) Singapore</option>
                        <option value="Asia/Taipei">(GMT+08:00) Taipei</option>
                        <option value="Asia/Ulaanbaatar">(GMT+08:00) Ulaan Bataar</option>
                        <option value="Asia/Urumqi">(GMT+08:00) Urumqi</option>
                        <option value="Asia/Irkutsk">(GMT+09:00) Irkutsk</option>
                        <option value="Asia/Seoul">(GMT+09:00) Seoul</option>
                        <option value="Asia/Tokyo">(GMT+09:00) Tokyo</option>
                        <option value="Australia/Adelaide">(GMT+09:30) Adelaide</option>
                        <option value="Australia/Darwin">(GMT+09:30) Darwin</option>
                        <option value="Asia/Yakutsk">(GMT+10:00) Yakutsk</option>
                        <option value="Australia/Brisbane">(GMT+10:00) Brisbane</option>
                        <option value="Australia/Canberra">(GMT+10:00) Canberra</option>
                        <option value="Pacific/Guam">(GMT+10:00) Guam</option>
                        <option value="Australia/Hobart">(GMT+10:00) Hobart</option>
                        <option value="Australia/Melbourne">(GMT+10:00) Melbourne</option>
                        <option value="Pacific/Port_Moresby">(GMT+10:00) Port Moresby</option>
                        <option value="Australia/Sydney">(GMT+10:00) Sydney</option>
                        <option value="Asia/Vladivostok">(GMT+11:00) Vladivostok</option>
                        <option value="Asia/Magadan">(GMT+12:00) Magadan</option>
                        <option value="Pacific/Auckland">(GMT+12:00) Auckland</option>
                        <option value="Pacific/Fiji">(GMT+12:00) Fiji</option>
                    </select>
                  </div>
                  <div class="form-group">
                    <label for="dateformat">Date Format</label> <a href="https://php.net/manual/en/function.date.php" target="_blank"><i class="fa fa-external-link"></i></a>
                    <input type="text" name="dateformat" value="jS F Y"  class="form-control tip" id="dateformat" required="required" />
                  </div>
                  <div class="form-group">
                    <label for="timeformat">Time Format</label>                                    <input type="text" name="timeformat" value="h:i A"  class="form-control tip" id="timeformat" required="required" />
                  </div>
                  <div class="form-group">
                    <label for="default_email">Default email</label>                                    <input type="text" name="default_email" value="noreply@spos.tecdiary.my"  class="form-control tip" id="default_email" required="required" />
                  </div>
                  <div class="form-group">
                    <label for="rtl">RTL Support</label>                                                                        
                    <select name="rtl" class="form-control select2" style="width:100%;" id="rtl">
                        <option value="0" selected="selected">Disable</option>
                        <option value="1">Enable</option>
                    </select>
                  </div>
              </div>
            </div>
            <div class="well well-sm">
              <div class="col-md-12">
                  <div class="form-group">
                    <label for="protocol">Email protocol</label>                                
                    <div class="controls">
                        <select name="protocol" class="form-control tip select2" id="protocol" style="width:100%;" required="required">
                          <option value="mail" selected="selected">PHP Mail Function</option>
                          <option value="sendmail">Send Mail</option>
                          <option value="smtp">SMTP</option>
                        </select>
                    </div>
                  </div>
              </div>
              <div class="clearfix"></div>
              <div class="row" id="sendmail_config" style="display: none;">
                  <div class="col-md-12">
                    <div class="col-md-6">
                        <div class="form-group">
                          <label for="mailpath">Mailpath</label>                                        
                          <div class="controls"> <input type="text" name="mailpath" value=""  class="form-control tip" id="mailpath" />
                          </div>
                        </div>
                    </div>
                  </div>
              </div>
              <div class="clearfix"></div>
              <div class="row" id="smtp_config" style="display: none;">
                  <div class="col-md-12">
                    <div class="col-md-6">
                        <div class="form-group">
                          <label for="smtp_host">SMTP Host</label>                                        
                          <div class="controls"> <input type="text" name="smtp_host" value="pop.gmail.com"  class="form-control tip" id="smtp_host" />
                          </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                          <label for="smtp_user">SMTP User</label>                                        
                          <div class="controls"> <input type="text" name="smtp_user" value="noreply@spos.tecdiary.my"  class="form-control tip" id="smtp_user" />
                          </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                          <label for="smtp_pass">SMTP Password</label>                                        
                          <div class="controls"> <input type="password" name="smtp_pass" value=""  class="form-control tip" id="smtp_pass" />
                          </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                          <label for="smtp_port">SMTP Port</label>                                        
                          <div class="controls"> <input type="text" name="smtp_port" value="25"  class="form-control tip" id="smtp_port" />
                          </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                          <label for="smtp_crypto">SMTP Crypto</label>                                        
                          <select name="smtp_crypto" class="form-control tip select2" id="smtp_crypto" style="width:100%;">
                              <option value="" selected="selected">None</option>
                              <option value="tls">TLS</option>
                              <option value="ssl">SSL</option>
                          </select>
                        </div>
                    </div>
                  </div>
              </div>
            </div>
            <div class="row">
              <div class="col-lg-12">
                  <div class="well well-sm">
                    <div class="col-md-4">
                        <div class="form-group">
                          <label class="control-label" for="decimals">Decimals</label>
                          <div class="controls">
                              <select name="decimals" class="form-control tip select2" id="decimals"  style="width:100%;" required="required">
                                <option value="0">Disable</option>
                                <option value="1">1</option>
                                <option value="2" selected="selected">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                              </select>
                          </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                          <label class="control-label" for="qty_decimals">Quantity Decimals</label>
                          <div class="controls">
                              <select name="qty_decimals" class="form-control tip select2" id="qty_decimals" style="width:100%;" required="required">
                                <option value="0">Disable</option>
                                <option value="1">1</option>
                                <option value="2" selected="selected">2</option>
                              </select>
                          </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                          <label for="sac">South Asian countries - currency format</label>                                                                                
                          <select name="sac" class="form-control tip select2" style="width:100%;" id="sac" required="required">
                              <option value="0" selected="selected">Disable</option>
                              <option value="1">Enable</option>
                          </select>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="nsac">
                        <div class="col-md-4">
                          <div class="form-group">
                              <label class="control-label" for="decimals_sep">Decimals Separator</label>
                              <div class="controls">
                                <select name="decimals_sep" class="form-control tip select2" id="decimals_sep" style="width:100%;" required="required">
                                    <option value="." selected="selected">Dot (.)</option>
                                    <option value=",">Comma (,)</option>
                                </select>
                              </div>
                          </div>
                        </div>
                        <div class="col-md-4">
                          <div class="form-group">
                              <label class="control-label" for="thousands_sep">Thousands Separator</label>
                              <div class="controls">
                                <select name="thousands_sep" class="form-control tip select2" id="thousands_sep" style="width:100%;" required="required">
                                    <option value=".">Dot (.)</option>
                                    <option value="," selected="selected">Comma (,)</option>
                                    <option value="0">Space</option>
                                </select>
                              </div>
                          </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                          <label for="display_symbol">Display Currency Symbol</label>                                                                                
                          <select name="display_symbol" class="form-control select2" id="display_symbol" style="width:100%;" required="required">
                              <option value="0" selected="selected">Disable</option>
                              <option value="1">Before</option>
                              <option value="2">After</option>
                          </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                          <label for="symbol">Currency Symbol</label>                                        <input type="text" name="symbol" value=""  class="form-control" id="symbol" style="width:100%;" />
                        </div>
                    </div>
                    <div class="clearfix"></div>
                  </div>
              </div>
            </div>
            <div class="row">
              <div class="col-lg-12">
                  <div class="well well-sm">
                    <div class="col-md-6">
                        <div class="form-group">
                          <label for="stripe">Stripe</label>                                                                                                
                          <select name="stripe" class="form-control select2" style="width:100%;" id="stripe" required="required">
                              <option value="0">Disable</option>
                              <option value="1" selected="selected">Enable</option>
                          </select>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div id="stripe_con">
                        <div class="col-md-6 col-sm-6">
                          <div class="form-group">
                              <label for="stripe_secret_key">Stripe Secret Key</label>                                                    <input type="text" name="stripe_secret_key" value="sk_test_jHf4cEzAYtgcXvgWPCsQAn50"  class="form-control tip" id="stripe_secret_key" />
                          </div>
                        </div>
                        <div class="col-md-6 col-sm-6">
                          <div class="form-group">
                              <label for="stripe_publishable_key">Stripe Publishable Key</label>                                                    <input type="text" name="stripe_publishable_key" value="pk_test_beat8SWPORb0OVdF2H77A7uG"  class="form-control tip" id="stripe_publishable_key" />
                          </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                  </div>
              </div>
            </div>
            <div class="row">
              <div class="col-lg-12">
                  <div class="well well-sm">
                    <p>Please set your shortcuts as you like, you can use F1 - F2 or any other key combinations with Crtl, Alt and Shift.</p>
                    <div class="col-md-4 col-sm-4">
                        <div class="form-group">
                          <label for="focus_add_item">Focus add/search item input</label>                                                <input type="text" name="focus_add_item" value="ALT+F1"  class="form-control tip" id="focus_add_item" />
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-4">
                        <div class="form-group">
                          <label for="add_customer">Add Customer</label>                                                <input type="text" name="add_customer" value="ALT+F2"  class="form-control tip" id="add_customer" />
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-4">
                        <div class="form-group">
                          <label for="toggle_category_slider">Toggle Category Slider</label>                                                <input type="text" name="toggle_category_slider" value="ALT+F10"  class="form-control tip" id="toggle_category_slider" />
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-4">
                        <div class="form-group">
                          <label for="cancel_sale">Cancel Sale</label>                                                <input type="text" name="cancel_sale" value="ALT+F5"  class="form-control tip" id="cancel_sale" />
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-4">
                        <div class="form-group">
                          <label for="suspend_sale">Suspend Sale</label>                                                <input type="text" name="suspend_sale" value="ALT+F6"  class="form-control tip" id="suspend_sale" />
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-4">
                        <div class="form-group">
                          <label for="print_order">Print Order</label>                                                <input type="text" name="print_order" value="ALT+F11"  class="form-control tip" id="print_order" />
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-4">
                        <div class="form-group">
                          <label for="print_bill">Print Bill</label>                                                <input type="text" name="print_bill" value="ALT+F12"  class="form-control tip" id="print_bill" />
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-4">
                        <div class="form-group">
                          <label for="finalize_sale">Finalize Sale</label>                                                <input type="text" name="finalize_sale" value="ALT+F8"  class="form-control tip" id="finalize_sale" />
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-4">
                        <div class="form-group">
                          <label for="today_sale">Today's Sale</label>                                                <input type="text" name="today_sale" value="Ctrl+F1"  class="form-control tip" id="today_sale" />
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-4">
                        <div class="form-group">
                          <label for="open_hold_bills">Opened Bills</label>                                                <input type="text" name="open_hold_bills" value="Ctrl+F2"  class="form-control tip" id="open_hold_bills" />
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-4">
                        <div class="form-group">
                          <label for="close_register">Close Register</label>                                                <input type="text" name="close_register" value="ALT+F7"  class="form-control tip" id="close_register" />
                        </div>
                    </div>
                    <div class="clearfix"></div>
                  </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                  <div class="form-group">
                    <label for="logo">Login Logo</label>                                        <input type="file" name="userfile" id="logo">
                  </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                  <div class="form-group">
                    <label for="remote_printing">Printing</label>                                                                                
                    <select name="remote_printing" class="form-control select2" id="remote_printing" style="width:100%;">
                        <option value="0">PHP Server (only for Localhost/Desktop)</option>
                        <option value="1" selected="selected">Web Browser</option>
                        <option value="2">PHP POS Print Server</option>
                    </select>
                    <span class="help-block">On local installation <strong>PHP Server</strong> will be the best choice and for live server, you can install <strong>PHP Pos Print Server</strong> locally.</span>
                    <span class="help-block ppp">You even can purchase <a href="http://tecdiary.com/products/php-pos-print-server-windows-installer" target="_blank">PHP POS Print Server (Windows Installer)</a>.</span>
                    <span class="help-block">On demo, you can test web printing only.</span>
                  </div>
              </div>
            </div>
            <div class="clearfix"></div>
            <div class="row">
              <div class="col-md-12">
                  <div class="well well-sm printers">
                    <div class="ppp">
                        <div class="col-md-6">
                          <div class="form-group">
                              <label for="local_printers">Use Local Printers</label>                                                                                                
                              <select name="local_printers" class="form-control select2" style="width:100%;" id="local_printers" required="required">
                                <option value="1" selected="selected">Yes</option>
                                <option value="0">No</option>
                              </select>
                          </div>
                        </div>
                    </div>
                    <div class="lp">
                        <div class="col-md-6">
                          <div class="form-group">
                              <label for="receipt_printer">Receipt Printer</label> <strong>*</strong>
                              <select name="receipt_printer" class="form-control select2" id="receipt_printer" style="width:100%;">
                              </select>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-group">
                              <label for="order_printers">Order Printers</label> <strong>*</strong>
                              <select name="order_printers[]" multiple class="form-control select2" id="order_printers" style="width:100%;">
                              </select>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-group">
                              <label for="cash_drawer_codes">Cash Drawer Code</label>                                                <input type="text" name="cash_drawer_codes" value=""  class="form-control" id="cash_drawer_codes" placeholder="\x1C" />
                          </div>
                        </div>
                    </div>
                    <div class="">
                        <div class="col-md-6">
                          <div class="form-group">
                              <label for="print_img">Send print as</label>                                                                                                
                              <select name="print_img" class="form-control select2" style="width:100%;" id="print_img" required="required">
                                <option value="0" selected="selected">Text</option>
                                <option value="1">Image</option>
                              </select>
                          </div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                  </div>
                  <div class="clearfix"></div>
              </div>
            </div>
          </form>
        </div>
        <div class="overlay hidden">
          <i class="fa fa-refresh fa-spin"></i>
        </div>
    </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{asset('adminlte/component/validate/jquery.validate.min.js')}}"></script>
<script>
  $(document).ready(function(){
      $('.select2').select2();
      $("#form").validate({
        errorElement: 'span',
        errorClass: 'help-block',
        focusInvalid: false,
        highlight: function (e) {
          $(e).closest('.form-group').removeClass('has-success').addClass('has-error');
        },
    
        success: function (e) {
          $(e).closest('.form-group').removeClass('has-error').addClass('has-success');
          $(e).remove();
        },
        errorPlacement: function (error, element) {
          if(element.is(':file')) {
            error.insertAfter(element.parent().parent().parent());
          }else
          if(element.parent('.input-group').length) {
            error.insertAfter(element.parent());
          } 
          else
          if (element.attr('type') == 'checkbox') {
            error.insertAfter(element.parent());
          }
          else{
            error.insertAfter(element);
          }
        },
        submitHandler: function() { 
          $.ajax({
            url:$('#form').attr('action'),
            method:'post',
            data: new FormData($('#form')[0]),
            processData: false,
            contentType: false,
            dataType: 'json', 
            beforeSend:function(){
               $('.overlay').removeClass('hidden');
            }
          }).done(function(response){
                $('.overlay').addClass('hidden');
                if(response.status){
                  document.location = response.results;
                }
                else{	
                  $.gritter.add({
                      title: 'Warning!',
                      text: response.message,
                      class_name: 'gritter-warning',
                      time: 1000,
                  });
                }
                return;
          }).fail(function(response){
              $('.overlay').addClass('hidden');
              var response = response.responseJSON;
              $.gritter.add({
                  title: 'Error!',
                  text: response.message,
                  class_name: 'gritter-error',
                  time: 1000,
              });
          })		
        }
      });
  });
</script>
@endpush
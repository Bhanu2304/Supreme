<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Auth;
use Session;
use App\AllocationDataMaster;
use App\Vicidial_List;
use App\Vicidial_Server;
use App\Vicidial_Postal_Codes;
use App\Vicidial_Phone_Codes;
use Illuminate\Support\Facades\DB;

class AllocationDataMasterImport implements ToModel
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public  $clientId=null,$user_id=null,$Allocation_Campaign_Id=null,$Allocation_Id=null,$Import_Date=null,$FileName=null,
            $ImportFields=null,$firstRow=true,$list_id=null,$call_type=null,$vendor_lead_code=1;
    
    public function __construct(String &$Campaign_Id,String &$Allocation_Id,String &$FileName,String &$ImportFields,String &$list_id,&$call_type)
    {
        $this->Allocation_Id = $Allocation_Id;
        $this->Allocation_Campaign_Id = $Campaign_Id;
        $this->FileName = $FileName;
        $this->ImportFields = $ImportFields;
        $this->list_id = $list_id;
        $this->call_type = $call_type;
        
        $this->clientId = Session::get('Client_Id');
        $this->user_id = Auth::user()->id;
        $this->Import_Date = date("Y-m-d H:i:s");
        
    }
    
    public function model(array $row)
    {
        $storeData = array();
        
        if(isset($row[1]) && !empty($row[1]) && !$this->firstRow)
        {
            $ImportFields = explode(",",$this->ImportFields);
            
            $i=1;
            $storeData = array('Allocation_Client_Id'    => $this->clientId, 
                'Allocation_Id'        => $this->Allocation_Id,
                'Allocation_Campaign_Id'         => $this->Allocation_Campaign_Id,
                'Allocation_List_Id'         => $this->list_id,
                'Import_Date'         => $this->Import_Date,
                'MSISDN'         => isset($row[0])?$row[0]:'');
                if($this->call_type=='Progressive')
                {
                    $Vicidial_List = new Vicidial_List();
                    $Vicidial_List->entry_date = $this->Import_Date;
                    $Vicidial_List->status = 'New';

                    $Vicidial_List->phone_number = $row[0];
                    $Vicidial_List->list_id = $this->list_id;
                    $Vicidial_List->vendor_lead_code = $this->vendor_lead_code++;
                    $Vicidial_List->source_id = $this->Allocation_Id;
                    $Vicidial_List->phone_code = 1;
                    $Vicidial_List->save();
                }
                
                //$Vicidial_List->phone_code = 1;
            //print_r($row); exit;

            for(;$i<=count($ImportFields); $i++)
            {
                $storeData['Field'.$i] = isset($row[$i])?$row[$i]:'';
            }

            $storeData['created_by'] = $this->user_id;
            
            
            $USarea = substr(trim($row[0]), 0, 3);
            $ViciDial_Server= json_decode(ViciDial_Server::selectRaw("local_gmt")->first(),true);
            $LOCAL_GMT_OFF_STD = $ViciDial_Server['local_gmt'];
            
            if (empty($LOCAL_GMT_OFF_STD) || $LOCAL_GMT_OFF_STD<0)
            {
                $LOCAL_GMT_OFF_STD = date("O");
                $LOCAL_GMT_OFF_STD = str_replace( '+', '', $LOCAL_GMT_OFF_STD);
                $LOCAL_GMT_OFF_STD = ($LOCAL_GMT_OFF_STD + 0);
                $LOCAL_GMT_OFF_STD = ($LOCAL_GMT_OFF_STD / 100);
            }
            $LOCAL_GMT_OFF_STD = (int)$LOCAL_GMT_OFF_STD;
            //getting gmt_offset from data
            $gmt_offset = $this->lookup_gmt($USarea,$LOCAL_GMT_OFF_STD); 
                      
            
            
            return new AllocationDataMaster($storeData);;
        }
        else
        {
            $this->firstRow=false;
        }
        //print_r($storeData); exit;
        
    }
    
    
public function lookup_gmt(&$USarea,&$LOCAL_GMT_OFF_STD)
{
    $phone_code = '1';
    $state = '';
    
    $postalgmt = 'POSTAL';
    $postal_code = '';
    $owner ="";
    $gmt_offset = $post = $postalgmt_found = $PC_processed = 0;
    global $link;
    
    $dst_range='';
    

    if ($postalgmt_found < 1)
    {
        $PC_processed=0;
        ### UNITED STATES ###
        if ($phone_code =='1')
        {
            $Vicidial_Phone_Codes = Vicidial_Phone_Codes::selectRaw("country_code,country,areacode,state,GMT_offset,DST,DST_range,geographic_description")->whereRaw("country_code='$phone_code' and areacode='$USarea'")->first();
            $phone_codes = json_decode($Vicidial_Phone_Codes,true);
            $pc_recs = is_array($phone_codes)?count($phone_codes):0;
            
            
            //print_r($pc_recs); exit;
            
            if($pc_recs<1)
            {
                $Vicidial_Phone_Codes = Vicidial_Phone_Codes::selectRaw("country_code,country,areacode,state,GMT_offset,DST,DST_range,geographic_description")->whereRaw("country_code='$phone_code'")->first();
                $phone_codes = json_decode($Vicidial_Phone_Codes,true);
                $pc_recs = is_array($phone_codes)?count($phone_codes):0;
                
                
                //print_r($phone_codes); exit;
            }

            if ($pc_recs > 0)
            {
                $gmt_offset =    $phone_codes['geographic_description'];     
                $gmt_offset = str_replace("+","",$gmt_offset);
                $dst =            $phone_codes['DST'];
                $dst_range =    $phone_codes['DST_range'];
                $PC_processed++;
            }
        }
        
    }
    
        $Shour=null;
//    ### Find out if DST to raise the gmt offset ###
        ### Find out if DST to raise the gmt offset ###
    $AC_GMT_diff = ((int)$gmt_offset - (int)$LOCAL_GMT_OFF_STD);
    $AC_localtime = mktime((date("H") + $AC_GMT_diff), date("i"), date("s"), date("m"), date("d"), date("Y")); 
        $hour = date("H",$AC_localtime);
        $min = date("i",$AC_localtime);
        $sec = date("s",$AC_localtime);
        $mon = date("m",$AC_localtime);
        $mday = date("d",$AC_localtime);
        $wday = date("w",$AC_localtime);
        $year = date("Y",$AC_localtime);
    $dsec = ( ( ($hour * 3600) + ($min * 60) ) + $sec );

    $AC_processed=0;
    if ( (!$AC_processed) and ($dst_range == 'SSM-FSN') )
        {
        
        //if ($DBX) {print "     Second Sunday March to First Sunday November\n";}
#**********************************************************************
        # SSM-FSN
        #     This is returns 1 if Daylight Savings Time is in effect and 0 if
        #       Standard time is in effect.
        #     Based on Second Sunday March to First Sunday November at 2 am.
        #     INPUTS:
        #       mm              INTEGER       Month.
        #       dd              INTEGER       Day of the month.
        #       ns              INTEGER       Seconds into the day.
        #       dow             INTEGER       Day of week (0=Sunday, to 6=Saturday)
        #     OPTIONAL INPUT:
        #       timezone        INTEGER       hour difference UTC - local standard time
        #                                      (DEFAULT is blank)
        #                                     make calculations based on UTC time,
        #                                     which means shift at 10:00 UTC in April
        #                                     and 9:00 UTC in October
        #     OUTPUT:
        #                       INTEGER       1 = DST, 0 = not DST
        #
        # S  M  T  W  T  F  S
        # 1  2  3  4  5  6  7
        # 8  9 10 11 12 13 14
        #15 16 17 18 19 20 21
        #22 23 24 25 26 27 28
        #29 30 31
        #
        # S  M  T  W  T  F  S
        #    1  2  3  4  5  6
        # 7  8  9 10 11 12 13
        #14 15 16 17 18 19 20
        #21 22 23 24 25 26 27
        #28 29 30 31
        #
#**********************************************************************

            $USACAN_DST=0;
            $mm = $mon;
            $dd = $mday;
            $ns = $dsec;
            $dow= $wday;

            if ($mm < 3 || $mm > 11) {
            $USACAN_DST=0;
            } elseif ($mm >= 4 and $mm <= 10) {
            $USACAN_DST=1;
            } elseif ($mm == 3) {
            if ($dd > 13) {
                $USACAN_DST=1;
            } elseif ($dd >= ($dow+8)) {
                if ($timezone) {
                if ($dow == 0 and $ns < (7200+$timezone*3600)) {
                    $USACAN_DST=0;
                } else {
                    $USACAN_DST=1;
                }
                } else {
                if ($dow == 0 and $ns < 7200) {
                    $USACAN_DST=0;
                } else {
                    $USACAN_DST=1;
                }
                }
            } else {
                $USACAN_DST=0;
            }
            } elseif ($mm == 11) {
            if ($dd > 7) {
                $USACAN_DST=0;
            } elseif ($dd < ($dow+1)) {
                $USACAN_DST=1;
            } elseif ($dow == 0) {
                if ($timezone) { # UTC calculations
                if ($ns < (7200+($timezone-1)*3600)) {
                    $USACAN_DST=1;
                } else {
                    $USACAN_DST=0;
                }
                } else { # local time calculations
                if ($ns < 7200) {
                    $USACAN_DST=1;
                } else {
                    $USACAN_DST=0;
                }
                }
            } else {
                $USACAN_DST=0;
            }
            } # end of month checks
        //if ($DBX) {print "     DST: $USACAN_DST\n";}
            
        if ($USACAN_DST) {$gmt_offset++;}
        $AC_processed++;
        }
        //echo $gmt_offset; exit;
//exit;
    if ( (!$AC_processed) and ($dst_range == 'FSA-LSO') )
        {
        //if ($DBX) {print "     First Sunday April to Last Sunday October\n";}
#**********************************************************************
        # FSA-LSO
        #     This is returns 1 if Daylight Savings Time is in effect and 0 if
        #       Standard time is in effect.
        #     Based on first Sunday in April and last Sunday in October at 2 am.
#**********************************************************************

            $USA_DST=0;
            $mm = $mon;
            $dd = $mday;
            $ns = $dsec;
            $dow= $wday;

            if ($mm < 4 || $mm > 10) {
            $USA_DST=0;
            } elseif ($mm >= 5 and $mm <= 9) {
            $USA_DST=1;
            } elseif ($mm == 4) {
            if ($dd > 7) {
                $USA_DST=1;
            } elseif ($dd >= ($dow+1)) {
                if ($timezone) {
                if ($dow == 0 and $ns < (7200+$timezone*3600)) {
                    $USA_DST=0;
                } else {
                    $USA_DST=1;
                }
                } else {
                if ($dow == 0 and $ns < 7200) {
                    $USA_DST=0;
                } else {
                    $USA_DST=1;
                }
                }
            } else {
                $USA_DST=0;
            }
            } elseif ($mm == 10) {
            if ($dd < 25) {
                $USA_DST=1;
            } elseif ($dd < ($dow+25)) {
                $USA_DST=1;
            } elseif ($dow == 0) {
                if ($timezone) { # UTC calculations
                if ($ns < (7200+($timezone-1)*3600)) {
                    $USA_DST=1;
                } else {
                    $USA_DST=0;
                }
                } else { # local time calculations
                if ($ns < 7200) {
                    $USA_DST=1;
                } else {
                    $USA_DST=0;
                }
                }
            } else {
                $USA_DST=0;
            }
            } # end of month checks

        //if ($DBX) {print "     DST: $USA_DST\n";}
        if ($USA_DST) {$gmt_offset++;}
        $AC_processed++;
        }

    if ( (!$AC_processed) and ($dst_range == 'LSM-LSO') )
        {
        //if ($DBX) {print "     Last Sunday March to Last Sunday October\n";}
#**********************************************************************
        #     This is s 1 if Daylight Savings Time is in effect and 0 if
        #       Standard time is in effect.
        #     Based on last Sunday in March and last Sunday in October at 1 am.
#**********************************************************************

            $GBR_DST=0;
            $mm = $mon;
            $dd = $mday;
            $ns = $dsec;
            $dow= $wday;

            if ($mm < 3 || $mm > 10) {
            $GBR_DST=0;
            } elseif ($mm >= 4 and $mm <= 9) {
            $GBR_DST=1;
            } elseif ($mm == 3) {
            if ($dd < 25) {
                $GBR_DST=0;
            } elseif ($dd < ($dow+25)) {
                $GBR_DST=0;
            } elseif ($dow == 0) {
                if ($timezone) { # UTC calculations
                if ($ns < (3600+($timezone-1)*3600)) {
                    $GBR_DST=0;
                } else {
                    $GBR_DST=1;
                }
                } else { # local time calculations
                if ($ns < 3600) {
                    $GBR_DST=0;
                } else {
                    $GBR_DST=1;
                }
                }
            } else {
                $GBR_DST=1;
            }
            } elseif ($mm == 10) {
            if ($dd < 25) {
                $GBR_DST=1;
            } elseif ($dd < ($dow+25)) {
                $GBR_DST=1;
            } elseif ($dow == 0) {
                if ($timezone) { # UTC calculations
                if ($ns < (3600+($timezone-1)*3600)) {
                    $GBR_DST=1;
                } else {
                    $GBR_DST=0;
                }
                } else { # local time calculations
                if ($ns < 3600) {
                    $GBR_DST=1;
                } else {
                    $GBR_DST=0;
                }
                }
            } else {
                $GBR_DST=0;
            }
            } # end of month checks
            //if ($DBX) {print "     DST: $GBR_DST\n";}
        if ($GBR_DST) {$gmt_offset++;}
        $AC_processed++;
        }
    if ( (!$AC_processed) and ($dst_range == 'LSO-LSM') )
        {
        //if ($DBX) {print "     Last Sunday October to Last Sunday March\n";}
#**********************************************************************
        #     This is s 1 if Daylight Savings Time is in effect and 0 if
        #       Standard time is in effect.
        #     Based on last Sunday in October and last Sunday in March at 1 am.
#**********************************************************************

            $AUS_DST=0;
            $mm = $mon;
            $dd = $mday;
            $ns = $dsec;
            $dow= $wday;

            if ($mm < 3 || $mm > 10) {
            $AUS_DST=1;
            } elseif ($mm >= 4 and $mm <= 9) {
            $AUS_DST=0;
            } elseif ($mm == 3) {
            if ($dd < 25) {
                $AUS_DST=1;
            } elseif ($dd < ($dow+25)) {
                $AUS_DST=1;
            } elseif ($dow == 0) {
                if ($timezone) { # UTC calculations
                if ($ns < (3600+($timezone-1)*3600)) {
                    $AUS_DST=1;
                } else {
                    $AUS_DST=0;
                }
                } else { # local time calculations
                if ($ns < 3600) {
                    $AUS_DST=1;
                } else {
                    $AUS_DST=0;
                }
                }
            } else {
                $AUS_DST=0;
            }
            } elseif ($mm == 10) {
            if ($dd < 25) {
                $AUS_DST=0;
            } elseif ($dd < ($dow+25)) {
                $AUS_DST=0;
            } elseif ($dow == 0) {
                if ($timezone) { # UTC calculations
                if ($ns < (3600+($timezone-1)*3600)) {
                    $AUS_DST=0;
                } else {
                    $AUS_DST=1;
                }
                } else { # local time calculations
                if ($ns < 3600) {
                    $AUS_DST=0;
                } else {
                    $AUS_DST=1;
                }
                }
            } else {
                $AUS_DST=1;
            }
            } # end of month checks
        //if ($DBX) {print "     DST: $AUS_DST\n";}
        if ($AUS_DST) {$gmt_offset++;}
        $AC_processed++;
        }

    if ( (!$AC_processed) and ($dst_range == 'FSO-LSM') )
        {
        //if ($DBX) {print "     First Sunday October to Last Sunday March\n";}
#**********************************************************************
        #   TASMANIA ONLY
        #     This is s 1 if Daylight Savings Time is in effect and 0 if
        #       Standard time is in effect.
        #     Based on first Sunday in October and last Sunday in March at 1 am.
#**********************************************************************

            $AUST_DST=0;
            $mm = $mon;
            $dd = $mday;
            $ns = $dsec;
            $dow= $wday;

            if ($mm < 3 || $mm > 10) {
            $AUST_DST=1;
            } elseif ($mm >= 4 and $mm <= 9) {
            $AUST_DST=0;
            } elseif ($mm == 3) {
            if ($dd < 25) {
                $AUST_DST=1;
            } elseif ($dd < ($dow+25)) {
                $AUST_DST=1;
            } elseif ($dow == 0) {
                if ($timezone) { # UTC calculations
                if ($ns < (3600+($timezone-1)*3600)) {
                    $AUST_DST=1;
                } else {
                    $AUST_DST=0;
                }
                } else { # local time calculations
                if ($ns < 3600) {
                    $AUST_DST=1;
                } else {
                    $AUST_DST=0;
                }
                }
            } else {
                $AUST_DST=0;
            }
            } elseif ($mm == 10) {
            if ($dd > 7) {
                $AUST_DST=1;
            } elseif ($dd >= ($dow+1)) {
                if ($timezone) {
                if ($dow == 0 and $ns < (7200+$timezone*3600)) {
                    $AUST_DST=0;
                } else {
                    $AUST_DST=1;
                }
                } else {
                if ($dow == 0 and $ns < 3600) {
                    $AUST_DST=0;
                } else {
                    $AUST_DST=1;
                }
                }
            } else {
                $AUST_DST=0;
            }
            } # end of month checks
        //if ($DBX) {print "     DST: $AUST_DST\n";}
        if ($AUST_DST) {$gmt_offset++;}
        $AC_processed++;
        }

    if ( (!$AC_processed) and ($dst_range == 'FSO-FSA') )
        {
        //if ($DBX) {print "     Sunday in October to First Sunday in April\n";}
#**********************************************************************
        # FSO-FSA
        #   2008+ AUSTRALIA ONLY (country code 61)
        #     This is returns 1 if Daylight Savings Time is in effect and 0 if
        #       Standard time is in effect.
        #     Based on first Sunday in October and first Sunday in April at 1 am.
#**********************************************************************

        $AUSE_DST=0;
        $mm = $mon;
        $dd = $mday;
        $ns = $dsec;
        $dow= $wday;

        if ($mm < 4 or $mm > 10) {
        $AUSE_DST=1;
        } elseif ($mm >= 5 and $mm <= 9) {
        $AUSE_DST=0;
        } elseif ($mm == 4) {
        if ($dd > 7) {
            $AUSE_DST=0;
        } elseif ($dd >= ($dow+1)) {
            if ($timezone) {
            if ($dow == 0 and $ns < (3600+$timezone*3600)) {
                $AUSE_DST=1;
            } else {
                $AUSE_DST=0;
            }
            } else {
            if ($dow == 0 and $ns < 7200) {
                $AUSE_DST=1;
            } else {
                $AUSE_DST=0;
            }
            }
        } else {
            $AUSE_DST=1;
        }
        } elseif ($mm == 10) {
        if ($dd >= 8) {
            $AUSE_DST=1;
        } elseif ($dd >= ($dow+1)) {
            if ($timezone) {
            if ($dow == 0 and $ns < (7200+$timezone*3600)) {
                $AUSE_DST=0;
            } else {
                $AUSE_DST=1;
            }
            } else {
            if ($dow == 0 and $ns < 3600) {
                $AUSE_DST=0;
            } else {
                $AUSE_DST=1;
            }
            }
        } else {
            $AUSE_DST=0;
        }
        } # end of month checks
        //if ($DBX) {print "     DST: $AUSE_DST\n";}
        if ($AUSE_DST) {$gmt_offset++;}
        $AC_processed++;
        }

    if ( (!$AC_processed) and ($dst_range == 'FSO-TSM') )
        {
        //if ($DBX) {print "     First Sunday October to Third Sunday March\n";}
#**********************************************************************
        #     This is s 1 if Daylight Savings Time is in effect and 0 if
        #       Standard time is in effect.
        #     Based on first Sunday in October and third Sunday in March at 1 am.
#**********************************************************************

            $NZL_DST=0;
            $mm = $mon;
            $dd = $mday;
            $ns = $dsec;
            $dow= $wday;

            if ($mm < 3 || $mm > 10) {
            $NZL_DST=1;
            } elseif ($mm >= 4 and $mm <= 9) {
            $NZL_DST=0;
            } elseif ($mm == 3) {
            if ($dd < 14) {
                $NZL_DST=1;
            } elseif ($dd < ($dow+14)) {
                $NZL_DST=1;
            } elseif ($dow == 0) {
                if ($timezone) { # UTC calculations
                if ($ns < (3600+($timezone-1)*3600)) {
                    $NZL_DST=1;
                } else {
                    $NZL_DST=0;
                }
                } else { # local time calculations
                if ($ns < 3600) {
                    $NZL_DST=1;
                } else {
                    $NZL_DST=0;
                }
                }
            } else {
                $NZL_DST=0;
            }
            } elseif ($mm == 10) {
            if ($dd > 7) {
                $NZL_DST=1;
            } elseif ($dd >= ($dow+1)) {
                if ($timezone) {
                if ($dow == 0 and $ns < (7200+$timezone*3600)) {
                    $NZL_DST=0;
                } else {
                    $NZL_DST=1;
                }
                } else {
                if ($dow == 0 and $ns < 3600) {
                    $NZL_DST=0;
                } else {
                    $NZL_DST=1;
                }
                }
            } else {
                $NZL_DST=0;
            }
            } # end of month checks
        //if ($DBX) {print "     DST: $NZL_DST\n";}
        if ($NZL_DST) {$gmt_offset++;}
        $AC_processed++;
        }

    if ( (!$AC_processed) and ($dst_range == 'LSS-FSA') )
        {
        //if ($DBX) {print "     Last Sunday in September to First Sunday in April\n";}
#**********************************************************************
        # LSS-FSA
        #   2007+ NEW ZEALAND (country code 64)
        #     This is returns 1 if Daylight Savings Time is in effect and 0 if
        #       Standard time is in effect.
        #     Based on last Sunday in September and first Sunday in April at 1 am.
#**********************************************************************

        $NZLN_DST=0;
        $mm = $mon;
        $dd = $mday;
        $ns = $dsec;
        $dow= $wday;

        if ($mm < 4 || $mm > 9) {
        $NZLN_DST=1;
        } elseif ($mm >= 5 && $mm <= 9) {
        $NZLN_DST=0;
        } elseif ($mm == 4) {
        if ($dd > 7) {
            $NZLN_DST=0;
        } elseif ($dd >= ($dow+1)) {
            if ($timezone) {
            if ($dow == 0 && $ns < (3600+$timezone*3600)) {
                $NZLN_DST=1;
            } else {
                $NZLN_DST=0;
            }
            } else {
            if ($dow == 0 && $ns < 7200) {
                $NZLN_DST=1;
            } else {

                $NZLN_DST=0;
            }
            }
        } else {
            $NZLN_DST=1;
        }
        } elseif ($mm == 9) {
        if ($dd < 25) {
            $NZLN_DST=0;
        } elseif ($dd < ($dow+25)) {
            $NZLN_DST=0;
        } elseif ($dow == 0) {
            if ($timezone) { # UTC calculations
            if ($ns < (3600+($timezone-1)*3600)) {
                $NZLN_DST=0;
            } else {
                $NZLN_DST=1;
            }
            } else { # local time calculations
            if ($ns < 3600) {
                $NZLN_DST=0;
            } else {
                $NZLN_DST=1;
            }
            }
        } else {
            $NZLN_DST=1;
        }
        } # end of month checks
        //if ($DBX) {print "     DST: $NZLN_DST\n";}
        if ($NZLN_DST) {$gmt_offset++;}
        $AC_processed++;
        }

    if ( (!$AC_processed) and ($dst_range == 'TSO-LSF') )
        {
        //if ($DBX) {print "     Third Sunday October to Last Sunday February\n";}
#**********************************************************************
        # TSO-LSF
        #     This is returns 1 if Daylight Savings Time is in effect and 0 if
        #       Standard time is in effect. Brazil
        #     Based on Third Sunday October to Last Sunday February at 1 am.
#**********************************************************************

            $BZL_DST=0;
            $mm = $mon;
            $dd = $mday;
            $ns = $dsec;
            $dow= $wday;

            if ($mm < 2 || $mm > 10) {
            $BZL_DST=1;
            } elseif ($mm >= 3 and $mm <= 9) {
            $BZL_DST=0;
            } elseif ($mm == 2) {
            if ($dd < 22) {
                $BZL_DST=1;
            } elseif ($dd < ($dow+22)) {
                $BZL_DST=1;
            } elseif ($dow == 0) {
                if ($timezone) { # UTC calculations
                if ($ns < (3600+($timezone-1)*3600)) {
                    $BZL_DST=1;
                } else {
                    $BZL_DST=0;
                }
                } else { # local time calculations
                if ($ns < 3600) {
                    $BZL_DST=1;
                } else {
                    $BZL_DST=0;
                }
                }
            } else {
                $BZL_DST=0;
            }
            } elseif ($mm == 10) {
            if ($dd < 22) {
                $BZL_DST=0;
            } elseif ($dd < ($dow+22)) {
                $BZL_DST=0;
            } elseif ($dow == 0) {
                if ($timezone) { # UTC calculations
                if ($ns < (3600+($timezone-1)*3600)) {
                    $BZL_DST=0;
                } else {
                    $BZL_DST=1;
                }
                } else { # local time calculations
                if ($ns < 3600) {
                    $BZL_DST=0;
                } else {
                    $BZL_DST=1;
                }
                }
            } else {
                $BZL_DST=1;
            }
            } # end of month checks
        //if ($DBX) {print "     DST: $BZL_DST\n";}
        if ($BZL_DST) {$gmt_offset++;}

        $AC_processed++;
        }

    if (!$AC_processed)
        {
        //if ($DBX) {print "     No DST Method Found\n";}
        //if ($DBX) {print "     DST: 0\n";}
        $AC_processed++;
        }

    return $gmt_offset;
    }  
}

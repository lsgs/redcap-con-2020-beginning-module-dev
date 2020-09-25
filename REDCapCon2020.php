<?php
/**
 * REDCap External Module: REDCapCon2020
 * Click the button to look up the max of the value entered and all previously entered values.
 * @author Luke Stevens, Murdoch Children's Research Institute
 */
namespace MCRI\REDCapCon2020;

use ExternalModules\AbstractExternalModule;

class REDCapCon2020 extends AbstractExternalModule
{
        public function redcap_data_entry_form_top($project_id, $record=null, $instrument, $event_id, $group_id=null, $repeat_instance=1) {
                // get url for the lookup page
                // it's not the directory path e.g. /modules_dev/redcapcon202_v0.0.0/value_lookup_ajax.php
                // but provided by the module framework e.g. /redcap_v?.?.?/ExternalModules/?prefix=redcapcon2020&page=value_lookup_ajax&pid=230
                $lookupUrl = $this->getUrl('value_lookup_ajax.php', false, false);
                $lookupField = $this->getProjectSetting('lookup-field');
                $setField = $this->getProjectSetting('set-field');
                
                // need both confg settings for it to work!
                if (empty($lookupField) || empty($setField)) { return; }
                
                //write some JavaScript to the page that will perform the task we want
                ?>
                <script type="text/javascript">
                    $(document).ready(function(){
                        // attach a click event handler to the button and show it
                        $('#DemoModuleLookup')
                            .on('click', function() {
                                // read the value entered in the lookup field
                                var entered = $('input[name=<?php echo $lookupField;?>]').val();

                                // send the entered value to the server and get back the max
                                $.post(
                                    '<?php echo $lookupUrl;?>', // where to send the data
                                    { value_entered: entered },  // the data to send (key-value pair object)
                                    function( data ) {        // the function to execute with the returned data
                                        // write the result to the destination field
                                        $('input[name=<?php echo $setField;?>]').val(data.result);
                                    }, 
                                    'json'
                                )
                                .fail(function() {
                                    alert('value lookup failed');
                                });
                                return false;
                            });
                    });
                </script>
                <?php
        }
		
        public function lookupMaxValue($thisValue) {
                $lookupField = $this->getProjectSetting('lookup-field');
                
                // read the existing data for the lookup field
                $previousValues = json_decode(\REDCap::getData(array(
                    'return_format' => 'json',
                    'fields' => $lookupField
                )), true);
                
                // loop through all previous valus and find the max
                $max = intval($thisValue);
                foreach ($previousValues as $rec) {
                        $max = (intval($rec[$lookupField])>$max)
                                ? intval($rec[$lookupField])
                                : $max;
                }
                return $max;
        }
}
<?php
/**
 * Class Saasu
 */
class Saasu extends Base
{
    /**
     * @var
     */
    public $url;
    /**
     * @var
     */
    public $token;
    /**
     * @var
     */
    public $fileId;
    /**
     * @var
     */
    public $staff;
    /**
     * @var
     */
    public $taxCode;
    /**
     * @var
     */
    public $layout;
    /**
     * @var
     */
    public $taxAccount;
    /**
     * @var
     */
    public $inventoryItemUid;
    /**
     * @var
     */
    public $fromEmail;
    /**
     * @var string
     */
    public $emailTemplate = 'invoice';
    /**
     * @var
     */
    public $hourlyRate;
    /**
     * @var
     */
    public $taxRate;
    /**
     * @var
     */
    public $contactId;
    /**
     * @var
     */
    public $profiles;
    /**
     * @var
     */
    public $sendEmail;
    /**
     * @var
     */
    public $errors;

    /**
     * @return array
     */
    public function getInvoices()
    {
        //return array(
        //    'an invoice'=>array(
        //        'to_email_address'=>'sales@mrphp.com.au',
        //        'saasu_contact_uid'=>'1894166',
        //        'date'=>date('Y-m-d'),
        //        'summary'=>'',
        //        'layout'=>$this->layout,
        //        'tags'=>'timesheet',
        //        'items'=>array(
        //            'an item'=>array(
        //                'description'=>'an invoice line',
        //                'quantity'=>'2',
        //                'amount'=>'99.99',
        //            ),
        //        ),
        //    ),
        //);

        $invoices = array();
        foreach ($this->staff as $staff => $staffInfo) {
            foreach (glob(bp() . '/data/GrindStone/timesheets/' . $staff . '/pending/*.tso') as $entry) {
                $this->errors = array();
                $path = pathinfo($entry);
                $timesheetFile = $path['basename'];
                if (file_exists(dirname(dirname($entry)) . '/archive/' . $timesheetFile)) {
                    continue;
                }
                $timesheet = unserialize(file_get_contents($entry));
                foreach ($timesheet->profiles as $profile) {

                    $contactId = $this->contactId;
                    if (isset($this->profiles[$profile->name]['contactId'])) {
                        $contactId = $this->profiles[$profile->name]['contactId'];
                    }

                    $toEmail = $this->fromEmail;
                    if (isset($this->profiles[$profile->name]['email'])) {
                        $toEmail = $this->profiles[$profile->name]['email'];
                    }

                    $hourlyRate = $staffInfo['rate'];
                    if (isset($staffInfo['profileRates'][$profile->name])) {
                        $hourlyRate = $staffInfo['profileRates'][$profile->name];
                    }

                    // build the invoice
                    if (!isset($invoices[$contactId])) {
                        $invoices[$contactId] = array(
                            'date' => date('Y-m-d'),
                            'date_due' => date('Y-m-d', strtotime('+7days')),
                            'profile_name' => $profile->name,
                            'to_email_address' => $toEmail,
                            'saasu_contact_uid' => $contactId,
                            'summary' => '',
                            'layout' => $this->layout,
                            'tags' => 'timesheet',
                            'items' => array(),
                            'times' => array(),
                        );
                    }

                    // build the items
                    if (!isset($invoices[$contactId]['items'][$profile->name])) {
                        $invoices[$contactId]['items'][$profile->name] = array(
                            'description' => "Hours for {$profile->name}",
                            'amount' => $hourlyRate,
                            'quantity' => 0,
                        );
                    }

                    // build the times
                    $timesheetDate = substr($timesheetFile, 0, -8);
                    if (!isset($invoices[$contactId]['times'][$staff . '|' . $profile->name][$timesheetDate])) {
                        $invoices[$contactId]['times'][$staff . '|' . $profile->name][$timesheetDate] = array();
                    }

                    // get the data from tasks
                    $multiplier = self::getStaffMultiplier($staff, $profile->name);
                    foreach ($profile->tasks as $task) {
                        $taskHours = 0;
                        foreach ($task->times as $time) {
                            $invoices[$contactId]['items'][$profile->name]['quantity'] += $time->hours * $multiplier;
                            $taskHours += $time->hours * $multiplier;
                        }
                        if (!isset($invoices[$contactId]['times'][$staff . '|' . $profile->name][$timesheetDate][$task->name])) {
                            $invoices[$contactId]['times'][$staff . '|' . $profile->name][$timesheetDate][$task->name] = 0;
                        }
                        $invoices[$contactId]['times'][$staff . '|' . $profile->name][$timesheetDate][$task->name] += number_format($taskHours, 2);
                    }

                }
            }
        }

        return $invoices;
    }

    /**
     * @param $invoices
     * @return array
     */
    public function createInvoices($invoices)
    {
        $tasks = array();
        foreach ($invoices as $invoice) {

            // insert or update
            $saasu_invoice_uid = false;
            $saasu_last_update_uid = false;
            if ($saasu_invoice_uid) {
                $task = 'updateInvoice';
                $attr = array(
                    'uid' => $saasu_invoice_uid,
                    'lastUpdatedUid' => $saasu_last_update_uid,
                );
            }
            else {
                $task = 'insertInvoice';
                $attr = array(
                    'uid' => 0,
                );
            }

            // items
            $invoiceItems = array();
            foreach ($invoice['items'] as $item) {
                if ($invoice['layout'] == 'S') {
                    $invoiceItems[] = array(
                        'serviceInvoiceItem' => array(
                            array('description' => array($item['description'])),
                            array('totalAmountInclTax' => array($item['amount'])),
                            array('accountUid' => array($this->taxAccount)),
                            array('taxCode' => array($this->taxCode)),
                        )
                    );
                }
                if ($invoice['layout'] == 'I') {
                    $invoiceItems[] = array(
                        'itemInvoiceItem' => array(
                            array('quantity' => array($item['quantity'])),
                            array('description' => array($item['description'])),
                            array('inventoryItemUid' => array($this->inventoryItemUid)),
                            array('unitPriceInclTax' => array($item['amount'])),
                            array('percentageDiscount' => array('0.00')),
                            array('taxCode' => array($this->taxCode)),
                        )
                    );
                }
            }

            // invoice
            $attributes = array();
            $emailMessage = array();
            if ($this->sendEmail) {
                $emailSubject = render('email/' . $this->emailTemplate . '.sbj', array('times' => $invoice['times']), true);
                $emailBody = render('email/' . $this->emailTemplate . '.txt', array('times' => $invoice['times']), true);
                $attributes = array('emailToContact' => 'true');
                $emailMessage = array(
                    array('from' => array($this->fromEmail)),
                    array('to' => array($invoice['to_email_address'])),
                    array('bcc' => array($this->fromEmail)),
                    array('subject' => array($emailSubject)),
                    array('body' => array($emailBody)),
                );
            }
            $tasks[] = array($task => array(
                '@attributes' => $attributes,
                array('invoice' => array(
                    '@attributes' => $attr,
                    array('transactionType' => array('S')), // S=sale P=purchase
                    array('date' => array($invoice['date'])),
                    array('dueOrExpiryDate' => array($invoice['date_due'])),
                    array('contactUid' => array($invoice['saasu_contact_uid'])),
                    array('tags' => array($invoice['tags'])),
                    array('summary' => array($invoice['summary'])),
                    array('layout' => array($invoice['layout'])),
                    array('status' => array('I')), // Q=quote O=order I=invoice
                    array('invoiceNumber' => array('<Auto Number>')),
                    array('invoiceItems' => $invoiceItems),
                )),
                array('emailMessage' => $emailMessage),
            ));
        }
        return $tasks;
    }

    /**
     * @param $tasks
     * @return array|bool
     */
    public function uploadInvoices($tasks)
    {
        $results = array();
        if (!empty($tasks)) {
            $results = $this->call('Tasks', null, $tasks);

            unset($results['@attributes']);
            foreach ($results as $k => $result) {
                $task = isset($tasks[$k]['updateInvoice']) ? 'updateInvoice' : 'insertInvoice';
                if (isset($result['errors'])) {
                    //error
                    debug($result['errors']);
                }
                elseif (isset($result[$task . 'Result'][0]['errors'])) {
                    //error
                    debug($result[$task . 'Result'][0]['errors']);
                }
                else {
                    // success
                    foreach ($this->staff as $staff => $staffInfo) {
                        foreach (glob(bp() . '/data/GrindStone/timesheets/' . $staff . '/pending/*.tso') as $entry) {
                            $path = pathinfo($entry);
                            $timesheetFile = $path['basename'];
                            $file = dirname(dirname($entry)) . '/archive/' . $timesheetFile;
                            if (!file_exists(dirname($file))) mkdir(dirname($file), 0777, true);
                            rename($entry, $file);
                        }
                    }
                }
            }
        }
        return $results;
    }

    /**
     * @param string $service
     * @param null $options
     * @param null $xml
     * @param string $xmlroot
     * @return bool|array
     */
    public function call($service = 'Tasks', $options = null, $xml = null, $xmlroot = 'tasks')
    {
        require_once(bp() . '/vendors/xml/xml.php');
        $url = $this->url . $service . '?WSAccessKey=' . $this->token . '&fileuid=' . $this->fileId;
        if (!empty($options)) {
            if (is_array($options)) {
                foreach ($options as $k => $option) {
                    $options[$k] = $k . '=' . $option;
                }
                $options = implode('&', $options);
            }
            $url .= '&' . $options;
        }

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        if ($xml) {
            if (is_array($xml)) {
                $parser = new xml();
                $xml = $parser->array2xml($xml, $xmlroot);
            }
            $tidy = new tidy;
            $tidy->parseString($xml, array(
                'indent' => true,
                'input-xml' => true,
                'output-xml' => true,
                'wrap' => 1000,
            ), 'utf8');
            //echo($tidy->value); die;
            //echo $xml; die;
            curl_setopt($ch, CURLOPT_POST, 1);
            //curl_setopt($ch, CURLOPT_POSTFIELDS,$xml);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $tidy->value);
        }
        //curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($ch);
        //echo(curl_error($ch));
        curl_close($ch);

        $parser = new xml();
        $parser->load_string($data);

        return $parser->xml2array();
    }

    /**
     * @param array $times
     * @return array
     */
    public function getProfits($times = array())
    {
        return $this->getTotals($times);
    }

    /**
     * @param array $times
     * @return array
     */
    public function getCosts($times = array())
    {
        return $this->getTotals($times, 'costs');
    }

    /**
     * @param array $times
     * @param string $type
     * @return array
     */
    public function getTotals($times = array(), $type = 'profits')
    {
        $profit = array();
        foreach ($times['daily'] as $date => $day) {
            if ($date == 'total') continue;
            foreach ($day['staff'] as $staff => $profiles) {
                if ($staff == 'total') continue;
                foreach ($profiles as $profile => $hours) {

                    // get the amount
                    $amount = 0;
                    if ($type == 'profits') {
                        $amount = $this->getStaffProfit($staff, $profile);
                    }
                    if ($type == 'costs') {
                        $amount = $this->getStaffCost($staff, $profile);
                    }
                    $amount = ($hours * $amount) / $this->getStaffTaxRate($staff, $profile);

                    // init the output
                    if (!isset($profit['total']['total']))
                        $profit['total']['total'] = 0;
                    if (!isset($profit['total']['staff'][$staff]))
                        $profit['total']['staff'][$staff] = 0;
                    if (!isset($profit['total']['profile'][$profile]))
                        $profit['total']['profile'][$profile] = 0;
                    if (!isset($profit['total'][$date]))
                        $profit['total'][$date] = 0;
                    if (!isset($profit[$date]['total']['staff'][$staff]))
                        $profit[$date]['total']['staff'][$staff] = 0;
                    if (!isset($profit[$date]['total']['profile'][$profile]))
                        $profit[$date]['total']['profile'][$profile] = 0;
                    if (!isset($profit[$date][$staff][$profile]))
                        $profit[$date][$staff][$profile] = 0;

                    // add to the output
                    $profit['total']['total'] += $amount;
                    $profit['total']['staff'][$staff] += $amount;
                    $profit['total']['profile'][$profile] += $amount;
                    $profit['total'][$date] += $amount;
                    $profit[$date]['total']['staff'][$staff] += $amount;
                    $profit[$date]['total']['profile'][$profile] += $amount;
                    $profit[$date][$staff][$profile] += $amount;
                }
            }
        }
        return $profit;
    }

    /**
     * @param $staff
     * @param null $profile
     * @return mixed
     */
    public function getStaffProfit($staff, $profile = null)
    {
        return $this->getStaffRate($staff, $profile) * $this->getStaffMultiplier($staff, $profile) - $this->getStaffCost($staff, $profile);
    }

    /**
     * @param $staff
     * @param null $profile
     * @return mixed
     */
    public function getStaffRate($staff, $profile = null)
    {
        $rate = $this->staff[$staff]['rate'];
        if ($profile && isset($this->staff[$staff]['profileRates'][$profile])) {
            $rate = $this->staff[$staff]['profileRates'][$profile];
        }
        return $rate;
    }


    /**
     * @param $staff
     * @param null $profile
     * @return mixed
     */
    public function getStaffCost($staff, $profile = null)
    {
        $cost = isset($this->staff[$staff]['cost']) ? $this->staff[$staff]['cost'] : 0;
        if ($profile && isset($this->staff[$staff]['profileCost'][$profile])) {
            $cost = $this->staff[$staff]['profileCost'][$profile];
        }
        return $cost;
    }

    /**
     * @param $staff
     * @param null $profile
     * @return mixed
     */
    public function getStaffTaxRate($staff, $profile = null)
    {
        $rate = 1.1;
        if (isset($this->staff[$staff]['taxRate'])) {
            $rate = $this->staff[$staff]['taxRate'];
        }
        if ($profile && isset($this->staff[$staff]['profileTaxRates'][$profile])) {
            $rate = $this->staff[$staff]['profileTaxRates'][$profile];
        }
        return $rate;
    }

    /**
     * @param $staff string
     * @param $project string
     * @return number
     */
    static public function getStaffMultiplier($staff, $project = null)
    {
        static $saasu = false;
        if (!$saasu) {
            $saasu = new Saasu(config('Saasu'));
        }

        if (isset($saasu->staff[$staff])) {
            if ($project && isset($saasu->staff[$staff]['profileMultiplier'][$project])) {
                return $saasu->staff[$staff]['profileMultiplier'][$project];
            }
            if (isset($saasu->staff[$staff]['multiplier'])) {
                return $saasu->staff[$staff]['multiplier'];
            }
        }
        return 1;
    }

}
<?php
class Mail{
    public static function getMail($user='', $password='') {
        $hostname = '{outlook.office365.com:993/imap/ssl}INBOX';
        $username = $user.'@ll.candea.nl';

        $data = array();
        /* try to connect */

        $inbox = imap_open($hostname,$username,$password) or die('Cannot connect to Outlook: ' . imap_last_error());

        /* grab emails */
        $emails = imap_search($inbox,'ALL');

        $count = 0;
        if (!$inbox) {
            echo "Error";
        } else {
            $headers = imap_headers($inbox);
            foreach ($headers as $mail) {
                $flags = substr($mail, 0, 4);
                $isunr = (strpos($flags, "U") !== false);
                if ($isunr)
                    $count++;
            }
        }

        $data['unread'] = $count

        /* if emails are returned, cycle through each... */
        if($emails) {

            /* begin output var */
            $output = '';

            /* put the newest emails on top */
            rsort($emails);

            /* for every email... */
            foreach($emails as $email_number) {

                /* get information specific to this email */
                $overview = imap_fetch_overview($inbox,$email_number,0);
                $message = imap_fetchbody($inbox,$email_number,2);

                /* output the email header information */
                $output.= '<div class="toggler '.($overview[0]->seen ? 'read' : 'unread').'">';
                $output.= '<span class="subject">'.$overview[0]->subject.'</span> ';
                $output.= '<span class="from">'.$overview[0]->from.'</span>';
                $output.= '<span class="date">on '.$overview[0]->date.'</span>';
                $output.= '</div>';

                /* output the email body */
        //$output.= '<div class="body">'.$message.'</div>';
            }

            echo $output;
        } 

        /* close the connection */
        imap_close($inbox);
        return $data;
    }
}

?>
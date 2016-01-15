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

        $unreadCount = 0;
        $emailsData = array();

        /* if emails are returned, cycle through each... */
        if($emails) {
            /* put the newest emails on top */
            rsort($emails);

            /* for every email... */
            foreach($emails as $email_number) {
                $mail = array();

                /* get information specific to this email */
                $overview = imap_fetch_overview($inbox,$email_number,0);
                $message = imap_fetchbody($inbox,$email_number,2);

                $read = $overview[0]->seen ? true : false;
                if(!$read){
                    $unreadCount++;
                }

                $mail['subject'] = $overview[0]->subject;
                $mail['sender'] = $overview[0]->from;
                $mail['read'] = $read;
                $mail['date'] = $overview[0]->date;
                $mail['message'] = $message;

                array_push($emailsData, $mail);
            }
        } 

        $data['unread'] = $unreadCount;
        $data['mails'] = $emailsData;

        /* close the connection */
        imap_close($inbox);
        return $data;
    }
}

?>

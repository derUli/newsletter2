<?php
namespace Newsletter;

use Model;
use NotImplementedException;
use Database;

class Subscriber extends Model
{

    private $email;

    private $confirmed = false;

    private $subscribe_date;

    public function loadByID($id)
    {
        $query = Database::pQuery("select * from `{prefix}newsletter_subscribers` where id = ?", array(
            $id
        ), true);
        if (Database::getNumRows($query)) {
            $this->fillVars($query);
        } else {
            $this->fillVars();
        }
    }

    public function loadByEmail($email)
    {
        $query = Database::pQuery("select * from `{prefix}newsletter_subscribers` where email = ?", array(
            $email
        ), true);
        if (Database::getNumRows($query)) {
            $this->fillVars($query);
        } else {
            $this->fillVars();
        }
    }

    protected function fillVars($query = null)
    {
        if ($query) {
            $data = Database::fetchObject($query);
            $this->setID($data->id);
            $this->setEmail($data->email);
            $this->setConfirmed($data->confirmed);
            $this->setSubscriptionDate($data->subscribe_date);
        } else {
            $this->setID(null);
            $this->setEmail(null);
            $this->setConfirmed(null);
            $this->setSubscriptionDate(null);
        }
    }

    protected function insert()
    {
        Database::pQuery("insert into `{prefix}newsletter_subscribers` (email, confirmed, subscribe_date)
                        values(?,?,?)", array(
            $this->email,
            $this->confirmed,
            $this->subscribe_date
        ), true);
        $this->setID(Database::getLastInsertID());
    }

    protected function update()
    {
        if (! $this->getID()) {
            return;
        }
        Database::pQuery("update `{prefix}newsletter_subscribers` set email = ?, confirmed = ?, subscribe_date = ? where id = ?", array(
            $this->email,
            $this->confirmed,
            $this->subscribe_date,
            $this->getID()
        ), true);
    }

    public function getConfirmationCode()
    {
        return md5($this->email . $this->getSubscribeDate());
    }

    public function confirm($code)
    {
        if ($code == $this->getConfirmationCode()) {
            $this->setConfirmed(true);
            $this->save();
            return true;
        }
        return false;
    }

    public function delete()
    {
        if (! $this->getID()) {
            return;
        }
        Database::pQuery("delete from `{prefix}newsletter_subscribers` where id = ?", array(
            $this->getID()
        ), true);
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getConfirmed()
    {
        return $this->confirmed;
    }

    public function getSubscribeDate()
    {
        return $this->subscribe_date;
    }

    public function setEmail($val)
    {
        $this->email = ! is_null($val) ? strval($val) : null;
    }

    public function setConfirmed($val)
    {
        $this->confirmed = ! is_null($val) ? boolval($val) : null;
    }

    public function setSubscriptionDate($val)
    {
        $this->subscribe_date = ! is_null($val) ? intval($val) : null;
    }
}
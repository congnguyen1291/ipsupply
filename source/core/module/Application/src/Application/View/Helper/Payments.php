<?php
namespace Application\View\Helper;
use Application\View\Helper\App;

class Payments extends App
{
	public function getPayments()
    {
        $payments = $this->getModelTable('UserTable')->getPaymentMethod();
        return $payments;
    }

    public function getPayment($payment_id)
    {
        $payment = $this->getModelTable('PaymentTable')->getPayment($payment_id);
        return $payment;
    }
}

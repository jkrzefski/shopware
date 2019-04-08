<?php
/**
 * Shopware 5
 * Copyright (c) shopware AG
 *
 * According to our dual licensing model, this program can be used either
 * under the terms of the GNU Affero General Public License, version 3,
 * or under a proprietary license.
 *
 * The texts of the GNU Affero General Public License with an additional
 * permission and of our proprietary license can be found at and
 * in the LICENSE file you have received along with this program.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * "Shopware" is a registered trademark of shopware AG.
 * The licensing of the program under the AGPLv3 does not imply a
 * trademark license. Therefore any rights, title and interest in
 * our trademarks remain entirely with us.
 */

class Migrations_Migration1612 extends Shopware\Components\Migrations\AbstractMigration
{
    private $sREGISTERCONFIRMATION_PLAIN = <<<'MAIL'
{include file="string:{config name=emailheaderplain}"}
        
Hallo {if $salutation != "none"}{$salutation|salutation} {/if}{$lastname},

vielen Dank für Ihre Anmeldung in unserem Shop.
Sie erhalten Zugriff über Ihre E-Mail-Adresse {$sMAIL} und dem von Ihnen gewählten Kennwort.
Sie können Ihr Kennwort jederzeit nachträglich ändern.

{include file="string:{config name=emailfooterplain}"}

MAIL;

    private $sREGISTERCONFIRMATION_HTML = <<<'MAIL'
<div style="font-family:arial; font-size:12px;">
    {include file="string:{config name=emailheaderhtml}"}
    <br/><br/>
    <p>
        Hallo {if $salutation != "none"}{$salutation|salutation} {/if}{$lastname},<br/>
        <br/>
        vielen Dank für Ihre Anmeldung in unserem Shop.<br/>
        Sie erhalten Zugriff über Ihre E-Mail-Adresse <strong>{$sMAIL}</strong> und dem von Ihnen gewählten Kennwort.<br/>
        Sie können Ihr Kennwort jederzeit nachträglich ändern.
    </p>
    {include file="string:{config name=emailfooterhtml}"}
</div>

MAIL;

    private $sORDER_PLAIN = <<<'MAIL'
{include file="string:{config name=emailheaderplain}"}
        
Hallo {if $billingaddress.salutation != "none"}{$billingaddress.salutation|salutation} {/if}{$billingaddress.lastname},

vielen Dank für Ihre Bestellung im {config name=shopName} (Nummer: {$sOrderNumber}) am {$sOrderDay} um {$sOrderTime}.
Informationen zu Ihrer Bestellung:

Pos.  Art.Nr.               Beschreibung                                      Menge       Preis       Summe
{foreach item=details key=position from=$sOrderDetails}
{{$position+1}|fill:4}  {$details.ordernumber|fill:20}  {$details.articlename|fill:49}  {$details.quantity|fill:6}  {$details.price|padding:8|currency|unescape:"htmlall"}      {$details.amount|padding:8|currency|unescape:"htmlall"}
{/foreach}

Versandkosten: {$sShippingCosts|currency|unescape:"htmlall"}
Gesamtkosten Netto: {$sAmountNet|currency|unescape:"htmlall"}
{if !$sNet}
{foreach $sTaxRates as $rate => $value}
zzgl. {$rate|number_format:0}% MwSt. {$value|currency|unescape:"htmlall"}
{/foreach}
Gesamtkosten Brutto: {$sAmount|currency|unescape:"htmlall"}
{/if}

Gewählte Zahlungsart: {$additional.payment.description}
{$additional.payment.additionaldescription}
{if $additional.payment.name == "debit"}
Ihre Bankverbindung:
Kontonr: {$sPaymentTable.account}
BLZ: {$sPaymentTable.bankcode}
Institut: {$sPaymentTable.bankname}
Kontoinhaber: {$sPaymentTable.bankholder}

Wir ziehen den Betrag in den nächsten Tagen von Ihrem Konto ein.
{/if}
{if $additional.payment.name == "prepayment"}

Unsere Bankverbindung:
Konto: ###
BLZ: ###
{/if}


Gewählte Versandart: {$sDispatch.name}
{$sDispatch.description}

{if $sComment}
Ihr Kommentar:
{$sComment}
{/if}

Rechnungsadresse:
{$billingaddress.company}
{$billingaddress.firstname} {$billingaddress.lastname}
{$billingaddress.street} {$billingaddress.streetnumber}
{if {config name=showZipBeforeCity}}{$billingaddress.zipcode} {$billingaddress.city}{else}{$billingaddress.city} {$billingaddress.zipcode}{/if}

{$additional.country.countryname}

Lieferadresse:
{$shippingaddress.company}
{$shippingaddress.firstname} {$shippingaddress.lastname}
{$shippingaddress.street} {$shippingaddress.streetnumber}
{if {config name=showZipBeforeCity}}{$shippingaddress.zipcode} {$shippingaddress.city}{else}{$shippingaddress.city} {$shippingaddress.zipcode}{/if}

{$additional.countryShipping.countryname}

{if $billingaddress.ustid}
Ihre Umsatzsteuer-ID: {$billingaddress.ustid}
Bei erfolgreicher Prüfung und sofern Sie aus dem EU-Ausland
bestellen, erhalten Sie Ihre Ware umsatzsteuerbefreit.
{/if}


Für Rückfragen stehen wir Ihnen jederzeit gerne zur Verfügung.

{include file="string:{config name=emailfooterplain}"}

MAIL;

    private $sORDER_HTML = <<<'MAIL'
<div style="font-family:arial; font-size:12px;">
    {include file="string:{config name=emailheaderhtml}"}
    <br/><br/>
    <p>Hallo {if $billingaddress.salutation != "none"}{$billingaddress.salutation|salutation} {/if}{$billingaddress.lastname},<br/>
        <br/>
        vielen Dank für Ihre Bestellung bei {config name=shopName} (Nummer: {$sOrderNumber}) am {$sOrderDay} um {$sOrderTime}.<br/>
        <br/>
        <strong>Informationen zu Ihrer Bestellung:</strong></p><br/>
    <table width="80%" border="0" style="font-family:Arial, Helvetica, sans-serif; font-size:12px;">
        <tr>
            <td bgcolor="#F7F7F2" style="border-bottom:1px solid #cccccc;"><strong>Pos.</strong></td>
            <td bgcolor="#F7F7F2" style="border-bottom:1px solid #cccccc;"><strong>Artikel</strong></td>
            <td bgcolor="#F7F7F2" style="border-bottom:1px solid #cccccc;">Bezeichnung</td>
            <td bgcolor="#F7F7F2" style="border-bottom:1px solid #cccccc;"><strong>Menge</strong></td>
            <td bgcolor="#F7F7F2" style="border-bottom:1px solid #cccccc;"><strong>Preis</strong></td>
            <td bgcolor="#F7F7F2" style="border-bottom:1px solid #cccccc;"><strong>Summe</strong></td>
        </tr>

        {foreach item=details key=position from=$sOrderDetails}
        <tr>
            <td style="border-bottom:1px solid #cccccc;">{$position+1|fill:4} </td>
            <td style="border-bottom:1px solid #cccccc;">{if $details.image.src.0 && $details.modus == 0}<img style="height: 57px;" height="57" src="{$details.image.src.0}" alt="{$details.articlename}" />{else} {/if}</td>
            <td style="border-bottom:1px solid #cccccc;">
              {$details.articlename|wordwrap:80|indent:4}<br>
              Artikel-Nr: {$details.ordernumber|fill:20}
            </td>
            <td style="border-bottom:1px solid #cccccc;">{$details.quantity|fill:6}</td>
            <td style="border-bottom:1px solid #cccccc;">{$details.price|padding:8|currency}</td>
            <td style="border-bottom:1px solid #cccccc;">{$details.amount|padding:8|currency}</td>
        </tr>
        {/foreach}

    </table>

    <p>
        <br/>
        <br/>
        Versandkosten: {$sShippingCosts|currency}<br/>
        Gesamtkosten Netto: {$sAmountNet|currency}<br/>
        {if !$sNet}
        {foreach $sTaxRates as $rate => $value}
        zzgl. {$rate|number_format:0}% MwSt. {$value|currency}<br/>
        {/foreach}
        <strong>Gesamtkosten Brutto: {$sAmount|currency}</strong><br/>
        {/if}
        <br/>
        <br/>
        <strong>Gewählte Zahlungsart:</strong> {$additional.payment.description}<br/>
        {$additional.payment.additionaldescription}
        {if $additional.payment.name == "debit"}
        Ihre Bankverbindung:<br/>
        Kontonr: {$sPaymentTable.account}<br/>
        BLZ: {$sPaymentTable.bankcode}<br/>
        Institut: {$sPaymentTable.bankname}<br/>
        Kontoinhaber: {$sPaymentTable.bankholder}<br/>
        <br/>
        Wir ziehen den Betrag in den nächsten Tagen von Ihrem Konto ein.<br/>
        {/if}
        <br/>
        <br/>
        {if $additional.payment.name == "prepayment"}
        Unsere Bankverbindung:<br/>
        Konto: ###<br/>
        BLZ: ###<br/>
        {/if}
        <br/>
        <br/>
        <strong>Gewählte Versandart:</strong> {$sDispatch.name}<br/>
        {$sDispatch.description}<br/>
    </p>
    <p>
        {if $sComment}
        <strong>Ihr Kommentar:</strong><br/>
        {$sComment}<br/>
        {/if}
        <br/>
        <br/>
        <strong>Rechnungsadresse:</strong><br/>
        {$billingaddress.company}<br/>
        {$billingaddress.firstname} {$billingaddress.lastname}<br/>
        {$billingaddress.street} {$billingaddress.streetnumber}<br/>
        {if {config name=showZipBeforeCity}}{$billingaddress.zipcode} {$billingaddress.city}{else}{$billingaddress.city} {$billingaddress.zipcode}{/if}<br/>
        {$additional.country.countryname}<br/>
        <br/>
        <br/>
        <strong>Lieferadresse:</strong><br/>
        {$shippingaddress.company}<br/>
        {$shippingaddress.firstname} {$shippingaddress.lastname}<br/>
        {$shippingaddress.street} {$shippingaddress.streetnumber}<br/>
        {if {config name=showZipBeforeCity}}{$shippingaddress.zipcode} {$shippingaddress.city}{else}{$shippingaddress.city} {$shippingaddress.zipcode}{/if}<br/>
        {$additional.countryShipping.countryname}<br/>
        <br/>
        {if $billingaddress.ustid}
        Ihre Umsatzsteuer-ID: {$billingaddress.ustid}<br/>
        Bei erfolgreicher Prüfung und sofern Sie aus dem EU-Ausland<br/>
        bestellen, erhalten Sie Ihre Ware umsatzsteuerbefreit.<br/>
        {/if}
        <br/>
        <br/>
        Für Rückfragen stehen wir Ihnen jederzeit gerne zur Verfügung.<br/>
        {include file="string:{config name=emailfooterhtml}"}
    </p>
</div>

MAIL;

    private $sORDERSTATEMAIL9_PLAIN = <<<'MAIL'
{include file="string:{config name=emailheaderplain}"}

Hallo {if $sUser.billing_salutation != "none"}{$sUser.billing_salutation|salutation} {/if}{$sUser.billing_lastname},

der Zahlungsstatus für Ihre Bestellung {$sOrder.ordernumber} vom {$sOrder.ordertime|date_format:"%d.%m.%Y"} hat sich geändert!
Die Bestellung hat jetzt den Zahlungsstatus: {$sOrder.cleared_description}.

Den aktuellen Status Ihrer Bestellung können Sie auch jederzeit auf unserer Webseite im  Bereich "Mein Konto" - "Meine Bestellungen" abrufen. Sollten Sie allerdings den Kauf ohne Registrierung, also ohne Anlage eines Kundenkontos, gewählt haben, steht Ihnen diese Möglichkeit nicht zur Verfügung.

{include file="string:{config name=emailfooterplain}"}

MAIL;

    private $sORDERSTATEMAIL9_HTML = <<<'MAIL'
<div style="font-family:arial; font-size:12px;">
    {include file="string:{config name=emailheaderhtml}"}
    <br/><br/>
    <p>
        Hallo {if $sUser.billing_salutation != "none"}{$sUser.billing_salutation|salutation} {/if}{$sUser.billing_lastname},<br/>
        <br/>
        der Zahlungsstatus für Ihre Bestellung {$sOrder.ordernumber} vom {$sOrder.ordertime|date_format:"%d.%m.%Y"} hat sich geändert!<br/>
        <strong>Die Bestellung hat jetzt den Zahlungsstatus: {$sOrder.cleared_description}.</strong><br/>
        <br/>
        Den aktuellen Status Ihrer Bestellung können Sie auch jederzeit auf unserer Webseite im  Bereich "Mein Konto" - "Meine Bestellungen" abrufen. Sollten Sie allerdings den Kauf ohne Registrierung, also ohne Anlage eines Kundenkontos, gewählt haben, steht Ihnen diese Möglichkeit nicht zur Verfügung.
    </p>
    {include file="string:{config name=emailfooterhtml}"}
</div>

MAIL;

    private $sORDERSTATEMAIL10_PLAIN = <<<'MAIL'
{include file="string:{config name=emailheaderplain}"}

Hallo {if $sUser.billing_salutation != "none"}{$sUser.billing_salutation|salutation} {/if}{$sUser.billing_lastname},

der Zahlungsstatus für Ihre Bestellung {$sOrder.ordernumber} vom {$sOrder.ordertime|date_format:"%d.%m.%Y"} hat sich geändert!
Die Bestellung hat jetzt den Zahlungsstatus: {$sOrder.cleared_description}.

Den aktuellen Status Ihrer Bestellung können Sie auch jederzeit auf unserer Webseite im  Bereich "Mein Konto" - "Meine Bestellungen" abrufen. Sollten Sie allerdings den Kauf ohne Registrierung, also ohne Anlage eines Kundenkontos, gewählt haben, steht Ihnen diese Möglichkeit nicht zur Verfügung.

{include file="string:{config name=emailfooterplain}"}

MAIL;

    private $sORDERSTATEMAIL10_HTML = <<<'MAIL'
<div style="font-family:arial; font-size:12px;">
    {include file="string:{config name=emailheaderhtml}"}
    <br/><br/>
    <p>
        Hallo {if $sUser.billing_salutation != "none"}{$sUser.billing_salutation|salutation} {/if}{$sUser.billing_lastname},<br/>
        <br/>
        der Zahlungsstatus für Ihre Bestellung {$sOrder.ordernumber} vom {$sOrder.ordertime|date_format:"%d.%m.%Y"} hat sich geändert!<br/>
        <strong>Die Bestellung hat jetzt den Zahlungsstatus: {$sOrder.cleared_description}.</strong><br/>
        <br/>
        Den aktuellen Status Ihrer Bestellung können Sie auch jederzeit auf unserer Webseite im  Bereich "Mein Konto" - "Meine Bestellungen" abrufen. Sollten Sie allerdings den Kauf ohne Registrierung, also ohne Anlage eines Kundenkontos, gewählt haben, steht Ihnen diese Möglichkeit nicht zur Verfügung.
    </p>
    {include file="string:{config name=emailfooterhtml}"}
</div>

MAIL;

    private $sORDERSTATEMAIL13_PLAIN = <<<'MAIL'
{include file="string:{config name=emailheaderplain}"}

Hallo {if $sUser.billing_salutation != "none"}{$sUser.billing_salutation|salutation} {/if}{$sUser.billing_lastname},

dies ist Ihre erste Mahnung zu Ihrer Bestellung {$sOrder.ordernumber} vom {$sOrder.ordertime|date_format:"%d.%m.%Y"}!
Die Bestellung hat jetzt den Zahlungsstatus: {$sOrder.cleared_description}.

Bitte begleichen Sie schnellstmöglich Ihre Rechnung!

Den aktuellen Status Ihrer Bestellung können Sie auch jederzeit auf unserer Webseite im  Bereich "Mein Konto" - "Meine Bestellungen" abrufen. Sollten Sie allerdings den Kauf ohne Registrierung, also ohne Anlage eines Kundenkontos, gewählt haben, steht Ihnen diese Möglichkeit nicht zur Verfügung.

{include file="string:{config name=emailfooterplain}"}

MAIL;

    private $sORDERSTATEMAIL13_HTML = <<<'MAIL'
<div style="font-family:arial; font-size:12px;">
    {include file="string:{config name=emailheaderhtml}"}
    <br/><br/>
    <p>
        Hallo {if $sUser.billing_salutation != "none"}{$sUser.billing_salutation|salutation} {/if}{$sUser.billing_lastname},<br/>
        <br/>
        dies ist Ihre erste Mahnung zu Ihrer Bestellung {$sOrder.ordernumber} vom {$sOrder.ordertime|date_format:"%d.%m.%Y"}!<br/>
        <strong>Die Bestellung hat jetzt den Zahlungsstatus: {$sOrder.cleared_description}.</strong><br/>
        <br/>
        <strong>Bitte begleichen Sie schnellstmöglich Ihre Rechnung!</strong><br/>
        <br/>
        Den aktuellen Status Ihrer Bestellung können Sie auch jederzeit auf unserer Webseite im  Bereich "Mein Konto" - "Meine Bestellungen" abrufen. Sollten Sie allerdings den Kauf ohne Registrierung, also ohne Anlage eines Kundenkontos, gewählt haben, steht Ihnen diese Möglichkeit nicht zur Verfügung.
    </p>
    {include file="string:{config name=emailfooterhtml}"}
</div>

MAIL;

    private $sORDERSTATEMAIL16_PLAIN = <<<'MAIL'
{include file="string:{config name=emailheaderplain}"}

Hallo {if $sUser.billing_salutation != "none"}{$sUser.billing_salutation|salutation} {/if}{$sUser.billing_lastname},

Sie haben inzwischen 3 Mahnungen zu Ihrer Bestellung {$sOrder.ordernumber} vom {$sOrder.ordertime|date_format:"%d.%m.%Y"} erhalten!
Die Bestellung hat jetzt den Zahlungsstatus: {$sOrder.cleared_description}.

Sie werden in Kürze Post von einem Inkasso Unternehmen erhalten!

Den aktuellen Status Ihrer Bestellung können Sie auch jederzeit auf unserer Webseite im  Bereich "Mein Konto" - "Meine Bestellungen" abrufen. Sollten Sie allerdings den Kauf ohne Registrierung, also ohne Anlage eines Kundenkontos, gewählt haben, steht Ihnen diese Möglichkeit nicht zur Verfügung.

{include file="string:{config name=emailfooterplain}"}

MAIL;

    private $sORDERSTATEMAIL16_HTML = <<<'MAIL'
<div style="font-family:arial; font-size:12px;">
    {include file="string:{config name=emailheaderhtml}"}
    <br/><br/>
    <p>
        Hallo {if $sUser.billing_salutation != "none"}{$sUser.billing_salutation|salutation} {/if}{$sUser.billing_lastname},<br/>
        <br/>
        Sie haben inzwischen 3 Mahnungen zu Ihrer Bestellung {$sOrder.ordernumber} vom {$sOrder.ordertime|date_format:"%d.%m.%Y"} erhalten!<br/>
        <strong>Die Bestellung hat jetzt den Zahlungsstatus: {$sOrder.cleared_description}.</strong><br/>
        <br/>
        <strong>Sie werden in Kürze Post von einem Inkasso Unternehmen erhalten!</strong><br/>
        <br/>
        Den aktuellen Status Ihrer Bestellung können Sie auch jederzeit auf unserer Webseite im  Bereich "Mein Konto" - "Meine Bestellungen" abrufen. Sollten Sie allerdings den Kauf ohne Registrierung, also ohne Anlage eines Kundenkontos, gewählt haben, steht Ihnen diese Möglichkeit nicht zur Verfügung.
    </p>
    {include file="string:{config name=emailfooterhtml}"}
</div>

MAIL;

    private $sORDERSTATEMAIL15_PLAIN = <<<'MAIL'
{include file="string:{config name=emailheaderplain}"}

Hallo {if $sUser.billing_salutation != "none"}{$sUser.billing_salutation|salutation} {/if}{$sUser.billing_lastname},

dies ist Ihre dritte und letzte Mahnung zu Ihrer Bestellung {$sOrder.ordernumber} vom {$sOrder.ordertime|date_format:"%d.%m.%Y"}!
Die Bestellung hat jetzt den Zahlungsstatus: {$sOrder.cleared_description}.

Bitte begleichen Sie schnellstmöglich Ihre Rechnung!

Den aktuellen Status Ihrer Bestellung können Sie auch jederzeit auf unserer Webseite im  Bereich "Mein Konto" - "Meine Bestellungen" abrufen. Sollten Sie allerdings den Kauf ohne Registrierung, also ohne Anlage eines Kundenkontos, gewählt haben, steht Ihnen diese Möglichkeit nicht zur Verfügung.

{include file="string:{config name=emailfooterplain}"}

MAIL;

    private $sORDERSTATEMAIL15_HTML = <<<'MAIL'
<div style="font-family:arial; font-size:12px;">
    {include file="string:{config name=emailheaderhtml}"}
    <br/><br/>
    <p>
        Hallo {if $sUser.billing_salutation != "none"}{$sUser.billing_salutation|salutation} {/if}{$sUser.billing_lastname},<br/>
        <br/>
        dies ist Ihre dritte und letzte Mahnung zu Ihrer Bestellung {$sOrder.ordernumber} vom {$sOrder.ordertime|date_format:"%d.%m.%Y"}!<br/>
        <strong>Die Bestellung hat jetzt den Zahlungsstatus: {$sOrder.cleared_description}.</strong><br/>
        <br/>
        <strong>Bitte begleichen Sie schnellstmöglich Ihre Rechnung!</strong><br/>
        <br/>
        Den aktuellen Status Ihrer Bestellung können Sie auch jederzeit auf unserer Webseite im  Bereich "Mein Konto" - "Meine Bestellungen" abrufen. Sollten Sie allerdings den Kauf ohne Registrierung, also ohne Anlage eines Kundenkontos, gewählt haben, steht Ihnen diese Möglichkeit nicht zur Verfügung.
    </p>
    {include file="string:{config name=emailfooterhtml}"}
</div>

MAIL;

    private $sORDERSTATEMAIL14_PLAIN = <<<'MAIL'
{include file="string:{config name=emailheaderplain}"}

Hallo {if $sUser.billing_salutation != "none"}{$sUser.billing_salutation|salutation} {/if}{$sUser.billing_lastname},

dies ist Ihre zweite Mahnung zu Ihrer Bestellung {$sOrder.ordernumber} vom {$sOrder.ordertime|date_format:"%d.%m.%Y"}!
Die Bestellung hat jetzt den Zahlungsstatus: {$sOrder.cleared_description}.

Bitte begleichen Sie schnellstmöglich Ihre Rechnung!

Den aktuellen Status Ihrer Bestellung können Sie auch jederzeit auf unserer Webseite im  Bereich "Mein Konto" - "Meine Bestellungen" abrufen. Sollten Sie allerdings den Kauf ohne Registrierung, also ohne Anlage eines Kundenkontos, gewählt haben, steht Ihnen diese Möglichkeit nicht zur Verfügung.

{include file="string:{config name=emailfooterplain}"}

MAIL;

    private $sORDERSTATEMAIL14_HTML = <<<'MAIL'
<div style="font-family:arial; font-size:12px;">
    {include file="string:{config name=emailheaderhtml}"}
    <br/><br/>
    <p>
        Hallo {if $sUser.billing_salutation != "none"}{$sUser.billing_salutation|salutation} {/if}{$sUser.billing_lastname},<br/>
        <br/>
        dies ist Ihre zweite Mahnung zu Ihrer Bestellung {$sOrder.ordernumber} vom {$sOrder.ordertime|date_format:"%d.%m.%Y"}!<br/>
        <strong>Die Bestellung hat jetzt den Zahlungsstatus: {$sOrder.cleared_description}.</strong><br/>
        <br/>
        <strong>Bitte begleichen Sie schnellstmöglich Ihre Rechnung!</strong><br/>
        <br/>
        Den aktuellen Status Ihrer Bestellung können Sie auch jederzeit auf unserer Webseite im  Bereich "Mein Konto" - "Meine Bestellungen" abrufen. Sollten Sie allerdings den Kauf ohne Registrierung, also ohne Anlage eines Kundenkontos, gewählt haben, steht Ihnen diese Möglichkeit nicht zur Verfügung.
    </p>
    {include file="string:{config name=emailfooterhtml}"}
</div>

MAIL;

    private $sORDERSTATEMAIL12_PLAIN = <<<'MAIL'
{include file="string:{config name=emailheaderplain}"}

Hallo {if $sUser.billing_salutation != "none"}{$sUser.billing_salutation|salutation} {/if}{$sUser.billing_lastname},

der Zahlungsstatus für Ihre Bestellung {$sOrder.ordernumber} vom {$sOrder.ordertime|date_format:"%d.%m.%Y"} hat sich geändert!
Die Bestellung hat jetzt den Zahlungsstatus: {$sOrder.cleared_description}.

Den aktuellen Status Ihrer Bestellung können Sie auch jederzeit auf unserer Webseite im  Bereich "Mein Konto" - "Meine Bestellungen" abrufen. Sollten Sie allerdings den Kauf ohne Registrierung, also ohne Anlage eines Kundenkontos, gewählt haben, steht Ihnen diese Möglichkeit nicht zur Verfügung.

{include file="string:{config name=emailfooterplain}"}

MAIL;

    private $sORDERSTATEMAIL12_HTML = <<<'MAIL'
<div style="font-family:arial; font-size:12px;">
    {include file="string:{config name=emailheaderhtml}"}
    <br/><br/>
    <p>
        Hallo {if $sUser.billing_salutation != "none"}{$sUser.billing_salutation|salutation} {/if}{$sUser.billing_lastname},<br/>
        <br/>
        der Zahlungsstatus für Ihre Bestellung {$sOrder.ordernumber} vom {$sOrder.ordertime|date_format:"%d.%m.%Y"} hat sich geändert!<br/>
        <strong>Die Bestellung hat jetzt den Zahlungsstatus: {$sOrder.cleared_description}.</strong><br/>
        <br/>
        Den aktuellen Status Ihrer Bestellung können Sie auch jederzeit auf unserer Webseite im  Bereich "Mein Konto" - "Meine Bestellungen" abrufen. Sollten Sie allerdings den Kauf ohne Registrierung, also ohne Anlage eines Kundenkontos, gewählt haben, steht Ihnen diese Möglichkeit nicht zur Verfügung.
    </p>
    {include file="string:{config name=emailfooterhtml}"}
</div>

MAIL;

    private $sORDERSTATEMAIL17_PLAIN = <<<'MAIL'
{include file="string:{config name=emailheaderplain}"}

Hallo {if $sUser.billing_salutation != "none"}{$sUser.billing_salutation|salutation} {/if}{$sUser.billing_lastname},

der Zahlungsstatus für Ihre Bestellung {$sOrder.ordernumber} vom {$sOrder.ordertime|date_format:"%d.%m.%Y"} hat sich geändert!
Die Bestellung hat jetzt den Zahlungsstatus: {$sOrder.cleared_description}.

Den aktuellen Status Ihrer Bestellung können Sie auch jederzeit auf unserer Webseite im  Bereich "Mein Konto" - "Meine Bestellungen" abrufen. Sollten Sie allerdings den Kauf ohne Registrierung, also ohne Anlage eines Kundenkontos, gewählt haben, steht Ihnen diese Möglichkeit nicht zur Verfügung.

{include file="string:{config name=emailfooterplain}"}

MAIL;

    private $sORDERSTATEMAIL17_HTML = <<<'MAIL'
<div style="font-family:arial; font-size:12px;">
    {include file="string:{config name=emailheaderhtml}"}
    <br/><br/>
    <p>
        Hallo {if $sUser.billing_salutation != "none"}{$sUser.billing_salutation|salutation} {/if}{$sUser.billing_lastname},<br/>
        <br/>
        der Zahlungsstatus für Ihre Bestellung {$sOrder.ordernumber} vom {$sOrder.ordertime|date_format:"%d.%m.%Y"} hat sich geändert!<br/>
        <strong>Die Bestellung hat jetzt den Zahlungsstatus: {$sOrder.cleared_description}.</strong><br/>
        <br/>
        Den aktuellen Status Ihrer Bestellung können Sie auch jederzeit auf unserer Webseite im  Bereich "Mein Konto" - "Meine Bestellungen" abrufen. Sollten Sie allerdings den Kauf ohne Registrierung, also ohne Anlage eines Kundenkontos, gewählt haben, steht Ihnen diese Möglichkeit nicht zur Verfügung.
    </p>
    {include file="string:{config name=emailfooterhtml}"}
</div>

MAIL;

    private $sORDERSTATEMAIL18_PLAIN = <<<'MAIL'
{include file="string:{config name=emailheaderplain}"}

Hallo {if $sUser.billing_salutation != "none"}{$sUser.billing_salutation|salutation} {/if}{$sUser.billing_lastname},

der Zahlungsstatus für Ihre Bestellung {$sOrder.ordernumber} vom {$sOrder.ordertime|date_format:"%d.%m.%Y"} hat sich geändert!
Die Bestellung hat jetzt den Zahlungsstatus: {$sOrder.cleared_description}.

Den aktuellen Status Ihrer Bestellung können Sie auch jederzeit auf unserer Webseite im  Bereich "Mein Konto" - "Meine Bestellungen" abrufen. Sollten Sie allerdings den Kauf ohne Registrierung, also ohne Anlage eines Kundenkontos, gewählt haben, steht Ihnen diese Möglichkeit nicht zur Verfügung.

{include file="string:{config name=emailfooterplain}"}

MAIL;

    private $sORDERSTATEMAIL18_HTML = <<<'MAIL'
<div style="font-family:arial; font-size:12px;">
    {include file="string:{config name=emailheaderhtml}"}
    <br/><br/>
    <p>
        Hallo {if $sUser.billing_salutation != "none"}{$sUser.billing_salutation|salutation} {/if}{$sUser.billing_lastname},<br/>
        <br/>
        der Zahlungsstatus für Ihre Bestellung {$sOrder.ordernumber} vom {$sOrder.ordertime|date_format:"%d.%m.%Y"} hat sich geändert!<br/>
        <strong>Die Bestellung hat jetzt den Zahlungsstatus: {$sOrder.cleared_description}.</strong><br/>
        <br/>
        Den aktuellen Status Ihrer Bestellung können Sie auch jederzeit auf unserer Webseite im  Bereich "Mein Konto" - "Meine Bestellungen" abrufen. Sollten Sie allerdings den Kauf ohne Registrierung, also ohne Anlage eines Kundenkontos, gewählt haben, steht Ihnen diese Möglichkeit nicht zur Verfügung.
    </p>
    {include file="string:{config name=emailfooterhtml}"}
</div>

MAIL;

    private $sORDERSTATEMAIL19_PLAIN = <<<'MAIL'
{include file="string:{config name=emailheaderplain}"}

Hallo {if $sUser.billing_salutation != "none"}{$sUser.billing_salutation|salutation} {/if}{$sUser.billing_lastname},

der Zahlungsstatus für Ihre Bestellung {$sOrder.ordernumber} vom {$sOrder.ordertime|date_format:"%d.%m.%Y"} hat sich geändert!
Die Bestellung hat jetzt den Zahlungsstatus: {$sOrder.cleared_description}.

Den aktuellen Status Ihrer Bestellung können Sie auch jederzeit auf unserer Webseite im  Bereich "Mein Konto" - "Meine Bestellungen" abrufen. Sollten Sie allerdings den Kauf ohne Registrierung, also ohne Anlage eines Kundenkontos, gewählt haben, steht Ihnen diese Möglichkeit nicht zur Verfügung.

{include file="string:{config name=emailfooterplain}"}

MAIL;

    private $sORDERSTATEMAIL19_HTML = <<<'MAIL'
<div style="font-family:arial; font-size:12px;">
    {include file="string:{config name=emailheaderhtml}"}
    <br/><br/>
    <p>
        Hallo {if $sUser.billing_salutation != "none"}{$sUser.billing_salutation|salutation} {/if}{$sUser.billing_lastname},<br/>
        <br/>
        der Zahlungsstatus für Ihre Bestellung {$sOrder.ordernumber} vom {$sOrder.ordertime|date_format:"%d.%m.%Y"} hat sich geändert!<br/>
        <strong>Die Bestellung hat jetzt den Zahlungsstatus: {$sOrder.cleared_description}.</strong><br/>
        <br/>
        Den aktuellen Status Ihrer Bestellung können Sie auch jederzeit auf unserer Webseite im  Bereich "Mein Konto" - "Meine Bestellungen" abrufen. Sollten Sie allerdings den Kauf ohne Registrierung, also ohne Anlage eines Kundenkontos, gewählt haben, steht Ihnen diese Möglichkeit nicht zur Verfügung.
    </p>
    {include file="string:{config name=emailfooterhtml}"}
</div>

MAIL;

    private $sORDERSTATEMAIL20_PLAIN = <<<'MAIL'
{include file="string:{config name=emailheaderplain}"}

Hallo {if $sUser.billing_salutation != "none"}{$sUser.billing_salutation|salutation} {/if}{$sUser.billing_lastname},

der Zahlungsstatus für Ihre Bestellung {$sOrder.ordernumber} vom {$sOrder.ordertime|date_format:"%d.%m.%Y"} hat sich geändert!
Die Bestellung hat jetzt den Zahlungsstatus: {$sOrder.cleared_description}.

Den aktuellen Status Ihrer Bestellung können Sie auch jederzeit auf unserer Webseite im  Bereich "Mein Konto" - "Meine Bestellungen" abrufen. Sollten Sie allerdings den Kauf ohne Registrierung, also ohne Anlage eines Kundenkontos, gewählt haben, steht Ihnen diese Möglichkeit nicht zur Verfügung.

{include file="string:{config name=emailfooterplain}"}

MAIL;

    private $sORDERSTATEMAIL20_HTML = <<<'MAIL'
<div style="font-family:arial; font-size:12px;">
    {include file="string:{config name=emailheaderhtml}"}
    <br/><br/>
    <p>
        Hallo {if $sUser.billing_salutation != "none"}{$sUser.billing_salutation|salutation} {/if}{$sUser.billing_lastname},<br/>
        <br/>
        der Zahlungsstatus für Ihre Bestellung {$sOrder.ordernumber} vom {$sOrder.ordertime|date_format:"%d.%m.%Y"} hat sich geändert!<br/>
        <strong>Die Bestellung hat jetzt den Zahlungsstatus: {$sOrder.cleared_description}.</strong><br/>
        <br/>
        Den aktuellen Status Ihrer Bestellung können Sie auch jederzeit auf unserer Webseite im  Bereich "Mein Konto" - "Meine Bestellungen" abrufen. Sollten Sie allerdings den Kauf ohne Registrierung, also ohne Anlage eines Kundenkontos, gewählt haben, steht Ihnen diese Möglichkeit nicht zur Verfügung.
    </p>
    {include file="string:{config name=emailfooterhtml}"}
</div>

MAIL;

    private $sCONFIRMPASSWORDCHANGE_PLAIN = <<<'MAIL'
{include file="string:{config name=emailheaderplain}"}

Hallo {if $user.salutation != "none"}{$user.salutation|salutation} {/if}{$user.lastname},

im Shop {$sShop} wurde eine Anfrage gestellt, um Ihr Passwort zurück zu setzen. Bitte bestätigen Sie den unten stehenden Link, um ein neues Passwort zu definieren.

{$sUrlReset}

Dieser Link ist nur für die nächsten 2 Stunden gültig. Danach muss das Zurücksetzen des Passwortes erneut beantragt werden. Falls Sie Ihr Passwort nicht zurücksetzen möchten, ignorieren Sie diese E-Mail - es wird dann keine Änderung vorgenommen.

{include file="string:{config name=emailfooterplain}"}

MAIL;

    private $sCONFIRMPASSWORDCHANGE_HTML = <<<'MAIL'
<div style="font-family:arial; font-size:12px;">
    {include file="string:{config name=emailheaderhtml}"}
    <br/><br/>
    <p>
        Hallo {if $user.salutation != "none"}{$user.salutation|salutation} {/if}{$user.lastname},<br/>
        <br/>
        im Shop {$sShop} wurde eine Anfrage gestellt, um Ihr Passwort zurück zu setzen.
        Bitte bestätigen Sie den unten stehenden Link, um ein neues Passwort zu definieren.<br/>
        <br/>
        <a href="{$sUrlReset}">Passwort zurücksetzen</a><br/>
        <br/>
        Dieser Link ist nur für die nächsten 2 Stunden gültig. Danach muss das Zurücksetzen des Passwortes erneut beantragt werden.
        Falls Sie Ihr Passwort nicht zurücksetzen möchten, ignorieren Sie diese E-Mail - es wird dann keine Änderung vorgenommen.
    </p>
    {include file="string:{config name=emailfooterhtml}"}
</div>

MAIL;

    private $sORDERSTATEMAIL1_PLAIN = <<<'MAIL'
{include file="string:{config name=emailheaderplain}"}

Hallo {if $sUser.billing_salutation != "none"}{$sUser.billing_salutation|salutation} {/if}{$sUser.billing_lastname},

der Bestellstatus für Ihre Bestellung {$sOrder.ordernumber} vom {$sOrder.ordertime|date_format:"%d.%m.%Y"} hat sich geändert!
Die Bestellung hat jetzt den Bestellstatus: {$sOrder.status_description}.

Den aktuellen Status Ihrer Bestellung können Sie auch jederzeit auf unserer Webseite im  Bereich "Mein Konto" - "Meine Bestellungen" abrufen. Sollten Sie allerdings den Kauf ohne Registrierung, also ohne Anlage eines Kundenkontos, gewählt haben, steht Ihnen diese Möglichkeit nicht zur Verfügung.

{include file="string:{config name=emailfooterplain}"}

MAIL;

    private $sORDERSTATEMAIL1_HTML = <<<'MAIL'
<div style="font-family:arial; font-size:12px;">
    {include file="string:{config name=emailheaderhtml}"}
    <br/><br/>
    <p>
        Hallo {if $sUser.billing_salutation != "none"}{$sUser.billing_salutation|salutation} {/if}{$sUser.billing_lastname},<br/>
        <br/>
        der Bestellstatus für Ihre Bestellung {$sOrder.ordernumber} vom {$sOrder.ordertime|date_format:"%d.%m.%Y"} hat sich geändert!<br/>
        <strong>Die Bestellung hat jetzt den Bestellstatus: {$sOrder.status_description}.</strong><br/>
        <br/>
        Den aktuellen Status Ihrer Bestellung können Sie auch jederzeit auf unserer Webseite im  Bereich "Mein Konto" - "Meine Bestellungen" abrufen. Sollten Sie allerdings den Kauf ohne Registrierung, also ohne Anlage eines Kundenkontos, gewählt haben, steht Ihnen diese Möglichkeit nicht zur Verfügung.
    </p>
    {include file="string:{config name=emailfooterhtml}"}
</div>

MAIL;

    private $sORDERSTATEMAIL2_PLAIN = <<<'MAIL'
{include file="string:{config name=emailheaderplain}"}

Hallo {if $sUser.billing_salutation != "none"}{$sUser.billing_salutation|salutation} {/if}{$sUser.billing_lastname},

der Bestellstatus für Ihre Bestellung {$sOrder.ordernumber} vom {$sOrder.ordertime|date_format:"%d.%m.%Y"} hat sich geändert!
Die Bestellung hat jetzt den Bestellstatus: {$sOrder.status_description}.

Den aktuellen Status Ihrer Bestellung können Sie auch jederzeit auf unserer Webseite im  Bereich "Mein Konto" - "Meine Bestellungen" abrufen. Sollten Sie allerdings den Kauf ohne Registrierung, also ohne Anlage eines Kundenkontos, gewählt haben, steht Ihnen diese Möglichkeit nicht zur Verfügung.

{include file="string:{config name=emailfooterplain}"}

MAIL;

    private $sORDERSTATEMAIL2_HTML = <<<'MAIL'
<div style="font-family:arial; font-size:12px;">
    {include file="string:{config name=emailheaderhtml}"}
    <br/><br/>
    <p>
        Hallo {if $sUser.billing_salutation != "none"}{$sUser.billing_salutation|salutation} {/if}{$sUser.billing_lastname},<br/>
        <br/>
        der Bestellstatus für Ihre Bestellung {$sOrder.ordernumber} vom {$sOrder.ordertime|date_format:"%d.%m.%Y"} hat sich geändert!<br/>
        <strong>Die Bestellung hat jetzt den Bestellstatus: {$sOrder.status_description}.</strong><br/>
        <br/>
        Den aktuellen Status Ihrer Bestellung können Sie auch jederzeit auf unserer Webseite im  Bereich "Mein Konto" - "Meine Bestellungen" abrufen. Sollten Sie allerdings den Kauf ohne Registrierung, also ohne Anlage eines Kundenkontos, gewählt haben, steht Ihnen diese Möglichkeit nicht zur Verfügung.
    </p>
    {include file="string:{config name=emailfooterhtml}"}
</div>

MAIL;

    private $sORDERSTATEMAIL11_PLAIN = <<<'MAIL'
{include file="string:{config name=emailheaderplain}"}

Hallo {if $sUser.billing_salutation != "none"}{$sUser.billing_salutation|salutation} {/if}{$sUser.billing_lastname},

der Zahlungsstatus für Ihre Bestellung {$sOrder.ordernumber} vom {$sOrder.ordertime|date_format:"%d.%m.%Y"} hat sich geändert!
Die Bestellung hat jetzt den Zahlungsstatus: {$sOrder.cleared_description}.

Den aktuellen Status Ihrer Bestellung können Sie auch jederzeit auf unserer Webseite im  Bereich "Mein Konto" - "Meine Bestellungen" abrufen. Sollten Sie allerdings den Kauf ohne Registrierung, also ohne Anlage eines Kundenkontos, gewählt haben, steht Ihnen diese Möglichkeit nicht zur Verfügung.

{include file="string:{config name=emailfooterplain}"}

MAIL;

    private $sORDERSTATEMAIL11_HTML = <<<'MAIL'
<div style="font-family:arial; font-size:12px;">
    {include file="string:{config name=emailheaderhtml}"}
    <br/><br/>
    <p>
        Hallo {if $sUser.billing_salutation != "none"}{$sUser.billing_salutation|salutation} {/if}{$sUser.billing_lastname},<br/>
        <br/>
        der Zahlungsstatus für Ihre Bestellung {$sOrder.ordernumber} vom {$sOrder.ordertime|date_format:"%d.%m.%Y"} hat sich geändert!<br/>
        <strong>Die Bestellung hat jetzt den Zahlungsstatus: {$sOrder.cleared_description}.</strong><br/>
        <br/>
        Den aktuellen Status Ihrer Bestellung können Sie auch jederzeit auf unserer Webseite im  Bereich "Mein Konto" - "Meine Bestellungen" abrufen. Sollten Sie allerdings den Kauf ohne Registrierung, also ohne Anlage eines Kundenkontos, gewählt haben, steht Ihnen diese Möglichkeit nicht zur Verfügung.
    </p>
    {include file="string:{config name=emailfooterhtml}"}
</div>

MAIL;

    private $sORDERSTATEMAIL5_PLAIN = <<<'MAIL'
{include file="string:{config name=emailheaderplain}"}

Hallo {if $sUser.billing_salutation != "none"}{$sUser.billing_salutation|salutation} {/if}{$sUser.billing_lastname},

der Bestellstatus für Ihre Bestellung {$sOrder.ordernumber} vom {$sOrder.ordertime|date_format:"%d.%m.%Y"} hat sich geändert!
Die Bestellung hat jetzt den Bestellstatus: {$sOrder.status_description}.

Den aktuellen Status Ihrer Bestellung können Sie auch jederzeit auf unserer Webseite im  Bereich "Mein Konto" - "Meine Bestellungen" abrufen. Sollten Sie allerdings den Kauf ohne Registrierung, also ohne Anlage eines Kundenkontos, gewählt haben, steht Ihnen diese Möglichkeit nicht zur Verfügung.

{include file="string:{config name=emailfooterplain}"}

MAIL;

    private $sORDERSTATEMAIL5_HTML = <<<'MAIL'
<div style="font-family:arial; font-size:12px;">
    {include file="string:{config name=emailheaderhtml}"}
    <br/><br/>
    <p>
        Hallo {if $sUser.billing_salutation != "none"}{$sUser.billing_salutation|salutation} {/if}{$sUser.billing_lastname},<br/>
        <br/>
        der Bestellstatus für Ihre Bestellung {$sOrder.ordernumber} vom {$sOrder.ordertime|date_format:"%d.%m.%Y"} hat sich geändert!<br/>
        <strong>Die Bestellung hat jetzt den Bestellstatus: {$sOrder.status_description}.</strong><br/>
        <br/>
        Den aktuellen Status Ihrer Bestellung können Sie auch jederzeit auf unserer Webseite im  Bereich "Mein Konto" - "Meine Bestellungen" abrufen. Sollten Sie allerdings den Kauf ohne Registrierung, also ohne Anlage eines Kundenkontos, gewählt haben, steht Ihnen diese Möglichkeit nicht zur Verfügung.
    </p>
    {include file="string:{config name=emailfooterhtml}"}
</div>

MAIL;

    private $sORDERSTATEMAIL3_PLAIN = <<<'MAIL'
{include file="string:{config name=emailheaderplain}"}

Hallo {if $sUser.billing_salutation != "none"}{$sUser.billing_salutation|salutation} {/if}{$sUser.billing_lastname},

der Bestellstatus für Ihre Bestellung {$sOrder.ordernumber} vom {$sOrder.ordertime|date_format:"%d.%m.%Y"} hat sich geändert!
Die Bestellung hat jetzt den Bestellstatus: {$sOrder.status_description}.


Informationen zu Ihrer Bestellung:
==================================
{foreach item=details key=position from=$sOrderDetails}
{$position+1|fill:3}      {$details.articleordernumber}     {$details.name|fill:30}     {$details.quantity} x {$details.price|string_format:"%.2f"} {$sOrder.currency}
{/foreach}

Versandkosten: {$sOrder.invoice_shipping|string_format:"%.2f"} {$sOrder.currency}
Netto-Gesamt: {$sOrder.invoice_amount_net|string_format:"%.2f"} {$sOrder.currency}
Gesamtbetrag inkl. MwSt.: {$sOrder.invoice_amount|string_format:"%.2f"} {$sOrder.currency}

Den aktuellen Status Ihrer Bestellung können Sie auch jederzeit auf unserer Webseite im  Bereich "Mein Konto" - "Meine Bestellungen" abrufen. Sollten Sie allerdings den Kauf ohne Registrierung, also ohne Anlage eines Kundenkontos, gewählt haben, steht Ihnen diese Möglichkeit nicht zur Verfügung.

{include file="string:{config name=emailfooterplain}"}

MAIL;

    private $sORDERSTATEMAIL3_HTML = <<<'MAIL'
<div style="font-family:arial; font-size:12px;">
    {include file="string:{config name=emailheaderhtml}"}
    <br/><br/>
    <p>
        Hallo {if $sUser.billing_salutation != "none"}{$sUser.billing_salutation|salutation} {/if}{$sUser.billing_lastname},<br/>
        <br/>
        der Bestellstatus für Ihre Bestellung {$sOrder.ordernumber} vom {$sOrder.ordertime|date_format:"%d.%m.%Y"} hat sich geändert!<br/>
        <strong>Die Bestellung hat jetzt den Bestellstatus: {$sOrder.status_description}.</strong><br/>
        <br/>
        <strong>Informationen zu Ihrer Bestellung:</strong></p><br/>
        <table width="80%" border="0" style="font-family:Arial, Helvetica, sans-serif; font-size:12px;">
            <tr>
                <td bgcolor="#F7F7F2" style="border-bottom:1px solid #cccccc;"><strong>Artikel</strong></td>
                <td bgcolor="#F7F7F2" style="border-bottom:1px solid #cccccc;"><strong>Pos.</strong></td>
                <td bgcolor="#F7F7F2" style="border-bottom:1px solid #cccccc;"><strong>Art-Nr.</strong></td>
                <td bgcolor="#F7F7F2" style="border-bottom:1px solid #cccccc;"><strong>Menge</strong></td>
                <td bgcolor="#F7F7F2" style="border-bottom:1px solid #cccccc;"><strong>Preis</strong></td>
            </tr>
            {foreach item=details key=position from=$sOrderDetails}
            <tr>
                <td>{$details.name|wordwrap:80|indent:4}</td>
                <td>{$position+1|fill:4} </td>
                <td>{$details.ordernumber|fill:20}</td>
                <td>{$details.quantity|fill:6}</td>
                <td>{$details.price|padding:8} {$sOrder.currency}</td>
            </tr>
            {/foreach}
        </table>
    <p>    
        <br/>
        Versandkosten: {$sOrder.invoice_shipping|string_format:"%.2f"} {$sOrder.currency}<br/>
        Netto-Gesamt: {$sOrder.invoice_amount_net|string_format:"%.2f"} {$sOrder.currency}<br/>
        Gesamtbetrag inkl. MwSt.: {$sOrder.invoice_amount|string_format:"%.2f"} {$sOrder.currency}<br/>
    	<br/>
        Den aktuellen Status Ihrer Bestellung können Sie auch jederzeit auf unserer Webseite im  Bereich "Mein Konto" - "Meine Bestellungen" abrufen. Sollten Sie allerdings den Kauf ohne Registrierung, also ohne Anlage eines Kundenkontos, gewählt haben, steht Ihnen diese Möglichkeit nicht zur Verfügung.
    </p>
    {include file="string:{config name=emailfooterhtml}"}
</div>

MAIL;

    private $sORDERSTATEMAIL8_PLAIN = <<<'MAIL'
{include file="string:{config name=emailheaderplain}"}

Hallo {if $sUser.billing_salutation != "none"}{$sUser.billing_salutation|salutation} {/if}{$sUser.billing_lastname},

der Bestellstatus für Ihre Bestellung {$sOrder.ordernumber} vom {$sOrder.ordertime|date_format:"%d.%m.%Y"} hat sich geändert!
Die Bestellung hat jetzt den Bestellstatus: {$sOrder.status_description}.

Den aktuellen Status Ihrer Bestellung können Sie auch jederzeit auf unserer Webseite im  Bereich "Mein Konto" - "Meine Bestellungen" abrufen. Sollten Sie allerdings den Kauf ohne Registrierung, also ohne Anlage eines Kundenkontos, gewählt haben, steht Ihnen diese Möglichkeit nicht zur Verfügung.

{include file="string:{config name=emailfooterplain}"}

MAIL;

    private $sORDERSTATEMAIL8_HTML = <<<'MAIL'
<div style="font-family:arial; font-size:12px;">
    {include file="string:{config name=emailheaderhtml}"}
    <br/><br/>
    <p>
        Hallo {if $sUser.billing_salutation != "none"}{$sUser.billing_salutation|salutation} {/if}{$sUser.billing_lastname},<br/>
        <br/>
        der Bestellstatus für Ihre Bestellung {$sOrder.ordernumber} vom {$sOrder.ordertime|date_format:"%d.%m.%Y"} hat sich geändert!<br/>
        <strong>Die Bestellung hat jetzt den Bestellstatus: {$sOrder.status_description}.</strong><br/>
        <br/>
        Den aktuellen Status Ihrer Bestellung können Sie auch jederzeit auf unserer Webseite im  Bereich "Mein Konto" - "Meine Bestellungen" abrufen. Sollten Sie allerdings den Kauf ohne Registrierung, also ohne Anlage eines Kundenkontos, gewählt haben, steht Ihnen diese Möglichkeit nicht zur Verfügung.
    </p>
    {include file="string:{config name=emailfooterhtml}"}
</div>

MAIL;

    private $sORDERSTATEMAIL4_PLAIN = <<<'MAIL'
{include file="string:{config name=emailheaderplain}"}

Hallo {if $sUser.billing_salutation != "none"}{$sUser.billing_salutation|salutation} {/if}{$sUser.billing_lastname},

der Bestellstatus für Ihre Bestellung {$sOrder.ordernumber} vom {$sOrder.ordertime|date_format:"%d.%m.%Y"} hat sich geändert!
Die Bestellung hat jetzt den Bestellstatus: {$sOrder.status_description}.

Den aktuellen Status Ihrer Bestellung können Sie auch jederzeit auf unserer Webseite im  Bereich "Mein Konto" - "Meine Bestellungen" abrufen. Sollten Sie allerdings den Kauf ohne Registrierung, also ohne Anlage eines Kundenkontos, gewählt haben, steht Ihnen diese Möglichkeit nicht zur Verfügung.

{include file="string:{config name=emailfooterplain}"}

MAIL;

    private $sORDERSTATEMAIL4_HTML = <<<'MAIL'
<div style="font-family:arial; font-size:12px;">
    {include file="string:{config name=emailheaderhtml}"}
    <br/><br/>
    <p>
        Hallo {if $sUser.billing_salutation != "none"}{$sUser.billing_salutation|salutation} {/if}{$sUser.billing_lastname},<br/>
        <br/>
        der Bestellstatus für Ihre Bestellung {$sOrder.ordernumber} vom {$sOrder.ordertime|date_format:"%d.%m.%Y"} hat sich geändert!<br/>
        <strong>Die Bestellung hat jetzt den Bestellstatus: {$sOrder.status_description}.</strong><br/>
        <br/>
        Den aktuellen Status Ihrer Bestellung können Sie auch jederzeit auf unserer Webseite im  Bereich "Mein Konto" - "Meine Bestellungen" abrufen. Sollten Sie allerdings den Kauf ohne Registrierung, also ohne Anlage eines Kundenkontos, gewählt haben, steht Ihnen diese Möglichkeit nicht zur Verfügung.
    </p>
    {include file="string:{config name=emailfooterhtml}"}
</div>

MAIL;

    private $sORDERSTATEMAIL6_PLAIN = <<<'MAIL'
{include file="string:{config name=emailheaderplain}"}

Hallo {if $sUser.billing_salutation != "none"}{$sUser.billing_salutation|salutation} {/if}{$sUser.billing_lastname},

der Bestellstatus für Ihre Bestellung {$sOrder.ordernumber} vom {$sOrder.ordertime|date_format:"%d.%m.%Y"} hat sich geändert!
Die Bestellung hat jetzt den Bestellstatus: {$sOrder.status_description}.

Den aktuellen Status Ihrer Bestellung können Sie auch jederzeit auf unserer Webseite im  Bereich "Mein Konto" - "Meine Bestellungen" abrufen. Sollten Sie allerdings den Kauf ohne Registrierung, also ohne Anlage eines Kundenkontos, gewählt haben, steht Ihnen diese Möglichkeit nicht zur Verfügung.

{include file="string:{config name=emailfooterplain}"}

MAIL;

    private $sORDERSTATEMAIL6_HTML = <<<'MAIL'
<div style="font-family:arial; font-size:12px;">
    {include file="string:{config name=emailheaderhtml}"}
    <br/><br/>
    <p>
        Hallo {if $sUser.billing_salutation != "none"}{$sUser.billing_salutation|salutation} {/if}{$sUser.billing_lastname},<br/>
        <br/>
        der Bestellstatus für Ihre Bestellung {$sOrder.ordernumber} vom {$sOrder.ordertime|date_format:"%d.%m.%Y"} hat sich geändert!<br/>
        <strong>Die Bestellung hat jetzt den Bestellstatus: {$sOrder.status_description}.</strong><br/>
        <br/>
        Den aktuellen Status Ihrer Bestellung können Sie auch jederzeit auf unserer Webseite im  Bereich "Mein Konto" - "Meine Bestellungen" abrufen. Sollten Sie allerdings den Kauf ohne Registrierung, also ohne Anlage eines Kundenkontos, gewählt haben, steht Ihnen diese Möglichkeit nicht zur Verfügung.
    </p>
    {include file="string:{config name=emailfooterhtml}"}
</div>

MAIL;

    private $sORDERSTATEMAIL7_PLAIN = <<<'MAIL'
{include file="string:{config name=emailheaderplain}"}

Hallo {if $sUser.billing_salutation != "none"}{$sUser.billing_salutation|salutation} {/if}{$sUser.billing_lastname},

der Bestellstatus für Ihre Bestellung {$sOrder.ordernumber} vom {$sOrder.ordertime|date_format:"%d.%m.%Y"} hat sich geändert!
Die Bestellung hat jetzt den Bestellstatus: {$sOrder.status_description}.

Den aktuellen Status Ihrer Bestellung können Sie auch jederzeit auf unserer Webseite im  Bereich "Mein Konto" - "Meine Bestellungen" abrufen. Sollten Sie allerdings den Kauf ohne Registrierung, also ohne Anlage eines Kundenkontos, gewählt haben, steht Ihnen diese Möglichkeit nicht zur Verfügung.

{include file="string:{config name=emailfooterplain}"}

MAIL;

    private $sORDERSTATEMAIL7_HTML = <<<'MAIL'
<div style="font-family:arial; font-size:12px;">
    {include file="string:{config name=emailheaderhtml}"}
    <br/><br/>
    <p>
        Hallo {if $sUser.billing_salutation != "none"}{$sUser.billing_salutation|salutation} {/if}{$sUser.billing_lastname},<br/>
        <br/>
        der Bestellstatus für Ihre Bestellung {$sOrder.ordernumber} vom {$sOrder.ordertime|date_format:"%d.%m.%Y"} hat sich geändert!<br/>
        <strong>Die Bestellung hat jetzt den Bestellstatus: {$sOrder.status_description}.</strong><br/>
        <br/>
        Den aktuellen Status Ihrer Bestellung können Sie auch jederzeit auf unserer Webseite im  Bereich "Mein Konto" - "Meine Bestellungen" abrufen. Sollten Sie allerdings den Kauf ohne Registrierung, also ohne Anlage eines Kundenkontos, gewählt haben, steht Ihnen diese Möglichkeit nicht zur Verfügung.
    </p>
    {include file="string:{config name=emailfooterhtml}"}
</div>

MAIL;

    private $sBIRTHDAY_PLAIN = <<<'MAIL'
{include file="string:{config name=emailheaderplain}"}

Hallo {if $sUser.salutation != "none"}{$sUser.salutation|salutation} {/if}{$sUser.lastname},
 
Alles Gute zum Geburtstag. Zu Ihrem persönlichen Jubiläum haben wir uns etwas Besonderes ausgedacht, wir senden Ihnen hiermit einen Geburtstagscode über {if $sVoucher.value}{$sVoucher.value|currency|unescape:"htmlall"}{else}{$sVoucher.percental} %{/if}, den Sie bei Ihrer nächsten Bestellung in unserem Online-Shop: {$sShopURL} ganz einfach einlösen können.
 
Ihr persönlicher Geburtstags-Code lautet: {$sVoucher.code}
{if $sVoucher.valid_from && $sVoucher.valid_to}Dieser Code ist gültig vom {$sVoucher.valid_from|date_format:"%d.%m.%Y"} bis zum {$sVoucher.valid_to|date_format:"%d.%m.%Y"}.{/if}
{if $sVoucher.valid_from && !$sVoucher.valid_to}Dieser Code ist gültig ab dem {$sVoucher.valid_from|date_format:"%d.%m.%Y"}.{/if}
{if !$sVoucher.valid_from && $sVoucher.valid_to}Dieser Code ist gültig bis zum {$sVoucher.valid_to|date_format:"%d.%m.%Y"}.{/if}


{include file="string:{config name=emailfooterplain}"}

MAIL;

    private $sBIRTHDAY_HTML = <<<'MAIL'
<div style="font-family:arial; font-size:12px;">
    {include file="string:{config name=emailheaderhtml}"}
    <br/><br/>
	<p>Hallo {if $sUser.salutation != "none"}{$sUser.salutation|salutation} {/if}{$sUser.lastname},</p>
 	<p><strong>Alles Gute zum Geburtstag</strong>. Zu Ihrem persönlichen Jubiläum haben wir uns etwas Besonderes ausgedacht, wir senden Ihnen hiermit einen Geburtstagscode über {if $sVoucher.value}{$sVoucher.value|currency|unescape:"htmlall"}{else}{$sVoucher.percental} %{/if}, den Sie bei Ihrer nächsten Bestellung in unserem <a href="{$sShopURL}" title="{$sShop}">Online-Shop</a> ganz einfach einlösen können.</p>
 	<p><strong>Ihr persönlicher Geburtstags-Code lautet: <span style="text-decoration:underline;">{$sVoucher.code}</span></strong><br/>
 	{if $sVoucher.valid_from && $sVoucher.valid_to}Dieser Code ist gültig vom {$sVoucher.valid_from|date_format:"%d.%m.%Y"} bis zum {$sVoucher.valid_to|date_format:"%d.%m.%Y"}.{/if}
 	{if $sVoucher.valid_from && !$sVoucher.valid_to}Dieser Code ist gültig ab dem {$sVoucher.valid_from|date_format:"%d.%m.%Y"}.{/if}
 	{if !$sVoucher.valid_from && $sVoucher.valid_to}Dieser Code ist gültig bis zum {$sVoucher.valid_to|date_format:"%d.%m.%Y"}.{/if}
</p>
 
    {include file="string:{config name=emailfooterhtml}"}
</div>

MAIL;

    private $sARTICLECOMMENT_PLAIN = <<<'MAIL'
{include file="string:{config name=emailheaderplain}"}

Hallo {if $sUser.billing_salutation != "none"}{$sUser.billing_salutation|salutation} {/if}{$sUser.billing_lastname},

Sie haben bei uns vor einigen Tagen Artikel gekauft. Wir würden uns freuen, wenn Sie diese Artikel bewerten würden.
So helfen Sie uns, unseren Service weiter zu steigern und Sie können auf diesem Weg anderen Interessenten direkt Ihre Meinung mitteilen.

Hier finden Sie die Links zum Bewerten der von Ihnen gekauften Produkte.

Bestellnummer     Artikelname     Bewertungslink
{foreach from=$sArticles item=sArticle key=key}
{if !$sArticle.modus}
{$sArticle.articleordernumber}      {$sArticle.name}      {$sArticle.link_rating_tab}
{/if}
{/foreach}

{include file="string:{config name=emailfooterplain}"}

MAIL;

    private $sARTICLECOMMENT_HTML = <<<'MAIL'
<div style="font-family:arial; font-size:12px;">
    {include file="string:{config name=emailheaderhtml}"}
    <br/><br/>
    Hallo {if $sUser.billing_salutation != "none"}{$sUser.billing_salutation|salutation} {/if}{$sUser.billing_lastname},<br/>
    <br/>
    Sie haben bei uns vor einigen Tagen Artikel gekauft. Wir würden uns freuen, wenn Sie diese Artikel bewerten würden.<br/>
    So helfen Sie uns, unseren Service weiter zu steigern und Sie können auf diesem Weg anderen Interessenten direkt Ihre Meinung mitteilen.<br/>
    <br/>
    Hier finden Sie die Links zum Bewerten der von Ihnen gekauften Produkte.<br/>
    <br/>
    <table width="80%" border="0" style="font-family:Arial, Helvetica, sans-serif; font-size:12px;">
        <tr>
          <td bgcolor="#F7F7F2" style="border-bottom:1px solid #cccccc;">Artikel</td>
          <td bgcolor="#F7F7F2" style="border-bottom:1px solid #cccccc;">Bestellnummer</td>
          <td bgcolor="#F7F7F2" style="border-bottom:1px solid #cccccc;">Artikelname</td>
          <td bgcolor="#F7F7F2" style="border-bottom:1px solid #cccccc;">Bewertungslink</td>
        </tr>
        {foreach from=$sArticles item=sArticle key=key}
        {if !$sArticle.modus}
            <tr>
                <td style="border-bottom:1px solid #cccccc;">
                  {if $sArticle.image_small && $sArticle.modus == 0}
                    <img style="height: 57px;" height="57" src="{$sArticle.image_small}" alt="{$sArticle.articlename}" />
                  {else}
                  {/if}
                </td>
                <td style="border-bottom:1px solid #cccccc;">{$sArticle.articleordernumber}</td>
                <td style="border-bottom:1px solid #cccccc;">{$sArticle.name}</td>
                <td style="border-bottom:1px solid #cccccc;">
                    <a href="{$sArticle.link_rating_tab}">Link</a>
                </td>
            </tr>
        {/if}
        {/foreach}
    </table>
    <br/><br/>
    {include file="string:{config name=emailfooterhtml}"}
</div>

MAIL;

    private $sORDERDOCUMENTS_PLAIN = <<<'MAIL'
{include file="string:{config name=emailheaderplain}"}

Hallo {if $sUser.salutation != "none"}{$sUser.salutation|salutation} {/if}{$sUser.lastname},

vielen Dank für Ihre Bestellung bei {config name=shopName}. Im Anhang finden Sie Dokumente zu Ihrer Bestellung als PDF.

{include file="string:{config name=emailfooterplain}"}

MAIL;

    private $sORDERDOCUMENTS_HTML = <<<'MAIL'
<div style="font-family:arial; font-size:12px;">
    {include file="string:{config name=emailheaderhtml}"}
    <br/><br/>
    <p>
        Hallo {if $sUser.salutation != "none"}{$sUser.salutation|salutation} {/if}{$sUser.lastname},<br/>
        <br/>
        vielen Dank für Ihre Bestellung bei {config name=shopName}. Im Anhang finden Sie Dokumente zu Ihrer Bestellung als PDF.
    </p>
    {include file="string:{config name=emailfooterhtml}"}
</div>

MAIL;

    private $document_invoice_PLAIN = <<<'MAIL'
{include file="string:{config name=emailheaderplain}"}

Hallo {if $sUser.salutation != "none"}{$sUser.salutation|salutation} {/if}{$sUser.lastname},

vielen Dank für Ihre Bestellung bei {config name=shopName}. Im Anhang finden Sie die Rechnung zu Ihrer Bestellung als PDF.

{include file="string:{config name=emailfooterplain}"}

MAIL;

    private $document_invoice_HTML = <<<'MAIL'
<div style="font-family:arial; font-size:12px;">
    {include file="string:{config name=emailheaderhtml}"}
    <br/><br/>
    <p>
        Hallo {if $sUser.salutation != "none"}{$sUser.salutation|salutation} {/if}{$sUser.lastname},<br/>
        <br/>
        vielen Dank für Ihre Bestellung bei {config name=shopName}. Im Anhang finden Sie die Rechnung zu Ihrer Bestellung als PDF.
    </p>
    {include file="string:{config name=emailfooterhtml}"}
</div>

MAIL;

    private $document_delivery_note_PLAIN = <<<'MAIL'
{include file="string:{config name=emailheaderplain}"}

Hallo {if $sUser.salutation != "none"}{$sUser.salutation|salutation} {/if}{$sUser.lastname},

vielen Dank für Ihre Bestellung bei {config name=shopName}. Im Anhang finden Sie den Lieferschein zu Ihrer Bestellung als PDF.

{include file="string:{config name=emailfooterplain}"}

MAIL;

    private $document_delivery_note_HTML = <<<'MAIL'
<div style="font-family:arial; font-size:12px;">
    {include file="string:{config name=emailheaderhtml}"}
    <br/><br/>
    <p>
        Hallo {if $sUser.salutation != "none"}{$sUser.salutation|salutation} {/if}{$sUser.lastname},<br/>
        <br/>
        vielen Dank für Ihre Bestellung bei {config name=shopName}. Im Anhang finden Sie den Lieferschein zu Ihrer Bestellung als PDF.
    </p>
    {include file="string:{config name=emailfooterhtml}"}
</div>

MAIL;

    private $document_credit_PLAIN = <<<'MAIL'
{include file="string:{config name=emailheaderplain}"}

Hallo {if $sUser.salutation != "none"}{$sUser.salutation|salutation} {/if}{$sUser.lastname},

vielen Dank für Ihre Bestellung bei {config name=shopName}. Im Anhang finden Sie die Gutschrift zu Ihrer Bestellung als PDF.

{include file="string:{config name=emailfooterplain}"}

MAIL;

    private $document_credit_HTML = <<<'MAIL'
<div style="font-family:arial; font-size:12px;">
    {include file="string:{config name=emailheaderhtml}"}
    <br/><br/>
    <p>
        Hallo {if $sUser.salutation != "none"}{$sUser.salutation|salutation} {/if}{$sUser.lastname},<br/>
        <br/>
        vielen Dank für Ihre Bestellung bei {config name=shopName}. Im Anhang finden Sie die Gutschrift zu Ihrer Bestellung als PDF.
    </p>
    {include file="string:{config name=emailfooterhtml}"}
</div>

MAIL;

    private $document_cancellation_PLAIN = <<<'MAIL'
{include file="string:{config name=emailheaderplain}"}

Hallo {if $sUser.salutation != "none"}{$sUser.salutation|salutation} {/if}{$sUser.lastname},

vielen Dank für Ihre Bestellung bei {config name=shopName}. Im Anhang finden Sie die Stornorechnung zu Ihrer Bestellung als PDF.
{include file="string:{config name=emailfooterplain}"}

MAIL;

    private $document_cancellation_HTML = <<<'MAIL'
<div style="font-family:arial; font-size:12px;">
    {include file="string:{config name=emailheaderhtml}"}
    <br/><br/>
    <p>
        Hallo {if $sUser.salutation != "none"}{$sUser.salutation|salutation} {/if}{$sUser.lastname},<br/>
        <br/>
        vielen Dank für Ihre Bestellung bei {config name=shopName}. Im Anhang finden Sie die Stornorechnung zu Ihrer Bestellung als PDF.
    </p>
    {include file="string:{config name=emailfooterhtml}"}
</div>

MAIL;

    const SQL = <<<'SQL'
UPDATE s_core_config_mails SET content = %s, contentHTML = %s WHERE name = %s AND dirty = 0;
SQL;

    const MAIL_TEMPLATES = [
        'sREGISTERCONFIRMATION',
        'sORDER',
        'sORDERSTATEMAIL9',
        'sORDERSTATEMAIL10',
        'sORDERSTATEMAIL13',
        'sORDERSTATEMAIL16',
        'sORDERSTATEMAIL15',
        'sORDERSTATEMAIL14',
        'sORDERSTATEMAIL12',
        'sORDERSTATEMAIL17',
        'sORDERSTATEMAIL18',
        'sORDERSTATEMAIL19',
        'sORDERSTATEMAIL20',
        'sCONFIRMPASSWORDCHANGE',
        'sORDERSTATEMAIL1',
        'sORDERSTATEMAIL2',
        'sORDERSTATEMAIL11',
        'sORDERSTATEMAIL5',
        'sORDERSTATEMAIL3',
        'sORDERSTATEMAIL8',
        'sORDERSTATEMAIL4',
        'sORDERSTATEMAIL6',
        'sORDERSTATEMAIL7',
        'sBIRTHDAY',
        'sARTICLECOMMENT',
        'sORDERDOCUMENTS',
        'document_invoice',
        'document_delivery_note',
        'document_credit',
        'document_cancellation',
    ];

    public function up($modus)
    {
        foreach (self::MAIL_TEMPLATES as $mailTemplate) {
            $content = $mailTemplate . '_PLAIN';
            $contentHtml = $mailTemplate . '_HTML';

            // TODO: use late binding
            $this->addSql(sprintf(self::SQL, $this->$content, $this->$contentHtml, $mailTemplate));

            // TODO: apply changes to translations
        }
    }
}

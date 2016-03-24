SYNERISE MAGENTO integration plugin
version: 0.9.


1. Vendor
1.1. Je�li do magento masz podpi�tego composera, wejd� na: https://github.com/Synerise/PHP-SDK i zastosuj si� do instrukcji na stronie.
Kolejny punk 1.2. pomi�.
1.2. Do pluginu magento zosta� dodany katalog "vendor" umie�� w g��wnym katalogu magento.


2. Instalacja pluginu magento.
2.1. Rozpakuj katalog app w g��wnym katalogu magento.
2.2. Plugin zawieta cztery modu�y mo�na je znale�� w \app\etc\modules. Wszystkie modu�y s� zale�ne od "Synerise_Integration.xml" - ten modu� musi by� w��czony je�li nawet u�ywasz tylko jednego z 
postoa�ych.
2.2.1 Modu�y
- Synerise_Integration - po w��czeniu nale�y w panelu magento "Synerise" -> "Inegracja" skonfigurowa� modu�. Musisz poda� odpowiedni klucz z API, w��czy� lub wy��czy� trackera oraz poda� 
klucz.
Modu� odpowiedzialny jest za wysy�anie event�w, mo�esz do woli w��czy� lub wy��cza� dany event z poziomu panelu. Konfigurowalna jest r�wnie� lista atrybut�w produkt�w, 
kt�ra b�dzie wysy�ana
do synerise. Mo�esz nie tylko w��czy� dany atrybut ale r�wnie� go mapowa� na dowolony klucz, kt�ry zostanie wys�any do synerise. WA�NE! Je�li dane s� ju� zbierane, 
zaleca si�
nie zmienia� mapowania atrybut�w. B�dzie to powodowa�o niesp�jno�� danych w synerise.
- Synerise_Coupon - Umo�liwa integracj� z systemem kupon�w.
- Synerise_Newsletter - Integracja z newsletterem sysnerise.
- Synerise_Export - Exportprodukt�w do xml

3. Logi
Mo�na dowolnie ustawi� �cie�k� logowania po przez:
$snr->setPathLog(Mage::getBaseDir('var') . DS . 'log' . DS . 'synerise.log');
Obecnie domy�lnie jest to �cie�ka g��wnego katalogu projektu do '/var/log/synerise.log'.


4. Modu�:Newsletter
4.1 Zapisanie si� do newslettera:
$api = Mage::getModel("synerise_newsletter/subscriber");
$api->subscribe($email, array('sex' => $sex));

5. Modu�:Kupony
$coupon = Mage::getModel('synerise_coupon/coupon');  
$coupon->setCouponCode($couponCode); 
$coupon->isSyneriseCoupon(); //sprawdza poprawno�� kodu i weryfikuje w synerise, czy kod mo�e by� u�yty 
$coupon->useCoupon(); // spalanie kuponyu

6. Modu�:Integration
Modu� ten po za zapisaniem kluczy API i Tracking s�u�y do zbierania danych.
Zbieranie danych odbywa si� na dw�ch poziomach. 
- tracking kody - Po w�aczeniu w panelu tracking kodu do strony zostanie dodany plik js. Kt�ry b�dzie wysy�a� informacj� o zachowaniu u�ytkownika na stronie.
- eventy - eventy s� wysy�ane z poziomu serwera, w panelu mageno jest mo�liwo�� dowolnego w�aczenia i wy��czenia danego eventu. Konfiguracja event�w znajduje si� w pliku \app\code
\community\Synerise\Integration\etc\config.xml
je�li masz mocno zmienion� �cie�k� zakupow� i nie s� wo�ane standardowe eventy nale�y to uwzgl�dni� w tym pliku.

7. Modu�:Export
Modu� s�u�y do eksportu katalogu. Po wygenerowaniu plik XML b�dzie znajdowa� si� w katalogu z mediami. Dodatkow nale�y ustawi� cz�stoliwo�� uruchamiania crona do generownaia xml domy�lnie cron b�dzie uruchamia� si� o 01:00 raz dziennie.
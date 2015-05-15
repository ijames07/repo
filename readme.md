Návod:
Aplikace je dostupná pro vyzkoušení na URL http://www.cafe.php5.cz/prihlasit, kde je možno přihlášení pomocí těchto uživatelských údajů:  
  
PRO GitHUB uživatele: MODEL DATABÁZE JE DOSTUPNÝ V SOUBORU czcafe.sql, soubory .zip už máte zde a nic nevybalujete    
  
Uživatelské jméno	|	heslo		|	role  
-------------------------------------------------  
zdar@zdar.cz		|	ahojahoj	|	zákazník  
ahoj				|	ahoj		|	zákazník  
employee@cafe.cz	|	ahoj		|	zaměstnanec  
manager@cafe.cz		|	ahoj		|	manažer  
  
-------------------------------------------------------------------------------------------------------------------------------------  
Aplikaci si můžete samozřejmě zprovoznit i na svém webovém serveru. Pro zprovoznění následujte tyto kroky:  
  
A) POŽADAVKY APLIKACE  
Webová aplikace požaduje webový server Apache 2.4 a novější, PHP 5.3.1 a novější, a MySQL (testováno na verzi 5.6.17 a 5.6.23).
Jestli Váš webový server splňuje požadavky můžete vyzkoušet nahráním složky Requirements-Checker ze souboru Aplikace.zip na server a spuštěním souboru checker.php  
  
  
B) ZPROVOZNĚNÍ DATABÁZE  
	1. Přihlaste se do administrátora databáze (např. phpMyAdmin)  
	2. Vytvořte novou databázi, například se jménem 'cafe' (bez apostrofů). Postup nyní v dalších krocích předpokládá tento název.  
	3. Vyberte tuto databázi (phpMyAdmin kliknutím vlevo na název nově vytvořené databáze - cafe  
	4. Z horizontální menu vyberte SQL, otevřete soubor Databáze.zip, extrahujte a následně otevřete soubor czcafe.sql. Zkopírujete obsah souboru a vložíte do textového pole v phpMyAdminu. Kliknutím na Proveď spustíte SQL dotaz, který jste si překopírovali ze souboru.  
	5. Nyní máte vytvořenou databázi a v ní ukázkový obsah.  
  
C) ZPROVOZNĚNÍ APLIKACE  
	1. Otevřete a rozbalte soubor Aplikace.zip na plochu  
	2. Otevřete soubor nette/app/config.local.neon  
	3. Zde upravte detaily připojení k db:  
		nazevdb 		= název databáze, v postupu jsme nastavili jméno cafe  
		uzivateldb 		= uživatelské jméno připojení k databázi  
		heslouzivatele 	= heslo uživatele pro připojení k databázi  
	pozn: Tento soubor je náchylný na netisknutelné znaky a má povolené pouze některé znaky. Pokud aplikace nefunguje kvůli souboru config.local.neon, zkontrolujte si zápis formátu v http://ne-on.org/. Obecně stačí nedělat mezery a přístupové údaje přepisovat pomocí dvojkliku na původní text. NEZAPOMEŇTE ULOŽIT!  
	4. Nyní můžete aplikaci nahrát na webový server.  
	5. Máte hotovo, můžete vyzkoušet.  
	  
	Aplikaci lze stáhnout i prostřednictvím GITu (vyžaduje GIT + BOWER):  
	Adresa repozitáře: https://github.com/ijames07/repo.git
	'git clone https://github.com/ijames07/repo.git'
	'git install'  
	Nyní je ještě potřeba stáhnout knihovny a css:  
	ve složce www/ spusťte 'bower install', pokud se Vás bower zeptá na verzi jQuery, zvolte 1.11.2 a novější.  
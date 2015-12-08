Upon completion, we will pay $250 in bitcoin to the contributor(s) of this repo.

The plugin is to be used as a payment module in the Tunga community which is currently being build using Buddypress. But ideally it is developed in a generic way so that it can be used for any Wordpress/Buddypress website.

Much of the required functionality is already available from the Chrome- and Q2A plugins, which can be found at the following locations:
https://github.com/mobbr/mobbr-plugin-chrome
You can see the Chrome plugin in action by installing it from the Google Chrome store and then go to a webpage which has Mobbr support such as Github.com or Stackoverflow.com, e.g. http://stackoverflow.com/questions/9234830/how-to-hide-a-option-in-a-select-menu-with-css or https://github.com/mobbr/mobbr-frontend/issues/308
By clicking on the Mobbr icon the Mobbr payment lightbox appears.

https://github.com/mobbr/mobbr-q2a-plugin
You can see the Q2A plugin in action here: http://www.fastmovingtargets.nl/workforce/295/voor-soort-beginnende-ondernemingen-passen-beste-startups
It's in Dutch but you'll get the idea. By clicking on the payment button the Mobbr payment lightbox appears.

For the Wordpress/Buddypress we need similar functionality. The current lightbox has 4 tabs. tab 2,3 and 4 can stay the same (payments, receivers, related).
In the payment tab there will be a few differences. For now, all payments will be done in bitcoin. the advantage is that the user who pays doesn't need to have a Mobbr account but can just send the btc to the address displayed in the lightbox. Mobbr will then distribute the btc among the Mobbr wallets of the recipient users. The payment tab of the lightbox will include the following elements: title of the task, amount to be paid in dollars, dollar amount converted to bitcoin, url of the task, brief instruction, pay from any bitcoin wallet, pay from mobbr bitcoin wallet, view payment details on Mobbr and payment preview.

Check the milestones and issues for required functionality and open tasks. In the issues the 'webmaster' is defined as the owner/manager of the Wordpress/Buddypress website and the 'user' are the visitors on the webmaster's website. If you have any questions, please let us know in the comment sections of that particular issue.


Issues

create basic buddypress plugin infrastructure & place buttons
- the Mobbr payment lightbox can be placed on posts and on pages. in the Mobbr plugin section in the admin the webmaster can check a box for posts and one for pages
- in the Mobbr plugin section in the admin environment, webmasters can choose whether they want to embed a Mobbr payment button icon or just a styled hyperlink button in order to open the Mobbr payment box. location of the button upon your discretion, or perhaps let the webmaster choose (top, bottom, in widget area).
- these options should be implemented on the actual posts/pages

remove currency options
- compared to the current lightbox (Q2A, Chrome) the currency options need not be included

include title of the task: this should be the post title (not really different)

calculate amount to be paid in dollars
- display as value the first numerical value mentioned in the post content

dollar amount converted to bitcoin
- based on realtime exchange rate. include hyperlink to some usd to bitcoin converter with text like 'convert another usd amount into btc'

url of the task: take the first url in the post content

include brief instruction text
- e.g. "Pay for this task by sending money to the bitcoin address below or via your Mobbr bitcoin wallet. The money will be distributed to the contributor(s) who worked on the task."

include 'pay from any bitcoin wallet' section: stays the same.

include and make changes to 'pay from mobbr bitcoin wallet' section
- include login to pay from mobbr bitcoin wallet button (same button as in current version)
- no dropdown for choice of currency (bitcoin only). 
- amount field shows the value from the abovementioned 'amount to be paid in dollars'. 
- no checkbox 'i want invoices'.
- including pay and cancel button (now displays only if you click on view payment details).

include view payment details on Mobbr
- a small linktext with a hyperlink to the corresponding task overview page on the mobbr website

include payment preview
- is displayed immediately upon opening of the lightbox, so no 'payment preview' button necessary
- will not be called 'payment preview' but 'Recipients'

implement payment logic
Now comes the most complex part (I think), the payment logic to be used is the following:
	- a % of the amount goes to an e-mail address. the webmaster can designate both % and e-mail address in the admin environment. the remaining % will be allocated as follows:
	- if task has url -> mobbr checks url
		if url has mobbr support (mobbr finds a script with a payment distribution key e.g. as on github, stackoverflow) -> the payment logic of that url is used (NB: for the remaining %, so applied to the amount minus the % taken by the webmaster/admin)
	  else there should be a form field asking the payer to enter the username or e-mail of the recipient(s) and behind that a box with a percentage (total of all recipients' shares should be 100%, if not use relative shares)

include an update button
- calculates a final payment preview before final confirmation of the payment

make plugin available via Wordpress

Address questions to: @efspruyt

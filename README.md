# Mage2 Module PayYourWay Pyw

    ``pay-your-way/module-pyw``

- [Main Functionalities](#markdown-header-main-functionalities)


## Main Functionalities
### API
 
* AccessTokenLookupInterface: Locator interface for AccessTokenRequestInterface service. 
* AccessTokenRequestInterface: Service interface that defines and encapsulates access token request. 
* ConfigInterfaceInterface: Interface used for managing PYW config configuration settings.
* GenerateAccessTokenInterface: Interface for creating/renewing Payyourway access token.
* OnboardingLookupInterface: Locator interface for OnboardingRequestInterface service.
* OnboardingRequestInterface: Service interface that defines and encapsulates methods PayYourWay onboarding request.
* PaymentConfirmationLookupInterface: Locator interface for (PayYourWay payment) Request service.
* RequestInterface: Service interface that defines and encapsulates methods for PayYourWay confirmation request.
* PaymentReturnLookupInterface: Locator interface for PaymentReturnRequestInterface service.
* PaymentReturnRequestInterface: Service interface that defines and encapsulates PayYourWay return request.
* RefIdBuilderInterface: Interface that defines method for building PayYourWay refid, used during payment authorization/authentication.
* UpdateMerchantLookupInterface: Locator interface for UpdateMerchantRequestInterface service.
* UpdateMerchantRequestInterface: Service interface that defines and encapsulates PayYourWay request to update existing merchant. 
  * Eg. Name, email, phone, address, category, domains.

### Onboarding Controller
Controller responsible for creating requests to PayYourWay onboarding API.
Used for onboarding new merchants. After merchant is created and during the same operation Merchant's additional details are saved. 

### Checkout Controller
Controllers responsible for checkout process. 
* PlaceOrder: controller responsible for initiating checkout, validation, order submission, and payment confirmation.
* Cancel: controller responsible for handling order cancellation.
* ReturnAction: controller responsible for handling return action.

### Cron
Magento Cron job that refreshes the access token to avoid expired token. Cron job is set to run every 105 minutes (1h30m). 

### Access Token Model
Model for retrieving AccessToken entity from table pyw_access_token.

### Payment Method Model
Model entity for PayYourWay payment method. Authorization, refund, capture functionality is implemented within this model.  

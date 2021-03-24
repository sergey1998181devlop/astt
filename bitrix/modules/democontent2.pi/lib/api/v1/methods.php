<?php
/**
 * Date: 14.07.2019
 * Time: 19:10
 * User: Ruslan Semagin
 * Company: PIXEL365
 * Web: https://pixel365.ru
 * Email: pixel.365.24@gmail.com
 * Phone: +7 (495) 005-23-76
 * Skype: pixel365
 * Product Page: https://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: https://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 * Use of this code is allowed only under the condition of full compliance with the terms of the license agreement,
 * and only as part of the product.
 */

namespace Democontent2\Pi\Api\V1;

abstract class Methods
{
    const LIST = [
        'auth' => ['alias' => '', 'method' => 'GET'],
        'balance' => ['alias' => '', 'method' => 'GET'],
        'cities' => ['alias' => '', 'method' => 'GET'],
        'categories' => ['alias' => '', 'method' => 'GET'],
        'user' => ['alias' => '', 'method' => 'GET'],
        'notifications' => ['alias' => '', 'method' => 'GET'],
        'properties' => ['alias' => '', 'method' => 'GET'],
        'task' => ['alias' => '', 'method' => 'GET'],
        'tasks' => ['alias' => '', 'method' => 'GET'],
        'blacklist' => ['alias' => 'BlackList', 'method' => 'GET'],
        'portfolioget' => ['alias' => 'PortfolioGet', 'method' => 'GET'],
        'portfoliolist' => ['alias' => 'PortfolioList', 'method' => 'GET'],
        'removefromblacklist' => ['alias' => 'RemoveFromBlackList', 'method' => 'GET'],
        'portfolioremovefile' => ['alias' => 'PortfolioRemoveFile', 'method' => 'GET'],
        'portfolioremove' => ['alias' => 'PortfolioRemove', 'method' => 'GET'],
        'addtoblacklist' => ['alias' => 'AddToBlackList', 'method' => 'GET'],
        'changefavourite' => ['alias' => 'ChangeFavourite', 'method' => 'GET'],
        'register' => ['alias' => '', 'method' => 'POST'],
        'portfolioadd' => ['alias' => 'PortfolioAdd', 'method' => 'POST'],
        'portfoliochangefiledescription' => ['alias' => 'PortfolioChangeFileDescription', 'method' => 'POST'],
        //'portfolioremovecategory' => 'PortfolioRemoveCategory',
        'edittask' => ['alias' => 'EditTask', 'method' => 'POST'],

        //TODO make documentation
        'reviews' => ['alias' => '', 'method' => 'GET'],
        'taskresponses' => ['alias' => 'TaskResponses', 'method' => 'GET'],
        'taskresponse' => ['alias' => 'TaskResponse', 'method' => 'GET'],
        'taskresponseexecutor' => ['alias' => 'TaskResponseExecutor', 'method' => 'GET'],
        'taskresponsereject' => ['alias' => 'TaskResponseReject', 'method' => 'GET'],
        'taskresponsecandidate' => ['alias' => 'TaskResponseCandidate', 'method' => 'GET'],
        'taskresponseblock' => ['alias' => 'TaskResponseBlock', 'method' => 'GET'],
        'offercost' => ['alias' => 'OfferCost', 'method' => 'GET'],
        'offerconfirm' => ['alias' => 'OfferConfirm', 'method' => 'GET'],
        'offerreject' => ['alias' => 'OfferReject', 'method' => 'GET'],
        'offercompleted' => ['alias' => 'OfferCompleted', 'method' => 'GET'],
        'createoffer' => ['alias' => 'CreateOffer', 'method' => 'POST'],
        'usercreatedtasks' => ['alias' => 'UserCreatedTasks', 'method' => 'GET'],
        'usercompletedtasks' => ['alias' => 'UserCompletedTasks', 'method' => 'GET'],
        'executortasks' => ['alias' => 'ExecutorTasks', 'method' => 'GET'],
        'changefavourites' => ['alias' => 'ChangeFavourites', 'method' => 'GET'],
        'favourites' => ['alias' => '', 'method' => 'GET'],
        'updateuser' => ['alias' => 'UpdateUser', 'method' => 'POST'],
        'createusertoken' => ['alias' => 'CreateUserToken', 'method' => 'POST'],
        'orderstatus' => ['alias' => 'OrderStatus', 'method' => 'GET'],
        'users' => ['alias' => '', 'method' => 'GET'],
        'completetask' => ['alias' => 'CompleteTask', 'method' => 'GET'],
        'createtask' => ['alias' => 'CreateTask', 'method' => 'POST'],
        'createorder' => ['alias' => 'CreateOrder', 'method' => 'POST'],
        'chatrooms' => ['alias' => 'ChatRooms', 'method' => 'GET'],
        'chatid' => ['alias' => 'ChatId', 'method' => 'GET'],
        'sendmessage' => ['alias' => 'SendMessage', 'method' => 'POST'],
        'portfoliouploadfiles' => ['alias' => 'PortfolioUploadFiles', 'method' => 'POST'],
        'complain' => ['alias' => '', 'method' => 'POST'],

        'restorepassword' => ['alias' => 'RestorePassword', 'method' => 'POST'],
    ];
}

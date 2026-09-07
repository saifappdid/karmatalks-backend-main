<?php

// @formatter:off
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * App\Models\AboutUs
 *
 * @property int $id
 * @property string $title
 * @property string $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|AboutUs newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AboutUs newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AboutUs query()
 * @method static \Illuminate\Database\Eloquent\Builder|AboutUs whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AboutUs whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AboutUs whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AboutUs whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AboutUs whereUpdatedAt($value)
 */
	class AboutUs extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\AppUser
 *
 * @property int $id
 * @property string|null $fullname
 * @property string|null $name
 * @property string|null $location
 * @property string $email
 * @property mixed|null $package
 * @property string|null $image
 * @property string|null $contact
 * @property int $wallet
 * @property string $type 0:user;1:staff;2:admin
 * @property string $used_referral 0:pending,1:used,2:never_used
 * @property string|null $lastseen
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $status
 * @property string|null $expiration
 * @property string|null $referral_code
 * @property int|null $referred_by
 * @property string|null $email_verified_at
 * @property string|null $password
 * @property string|null $remember_token
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Laravel\Sanctum\PersonalAccessToken[] $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\AppUserFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|AppUser newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AppUser newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AppUser query()
 * @method static \Illuminate\Database\Eloquent\Builder|AppUser whereContact($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppUser whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppUser whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppUser whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppUser whereExpiration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppUser whereFullname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppUser whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppUser whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppUser whereLastseen($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppUser whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppUser whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppUser wherePackage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppUser wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppUser whereReferralCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppUser whereReferredBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppUser whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppUser whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppUser whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppUser whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppUser whereUsedReferral($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppUser whereWallet($value)
 */
	class AppUser extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Blog
 *
 * @property int $id
 * @property string $title
 * @property string $tag
 * @property string $description
 * @property string $image
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Database\Factories\BlogFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Blog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Blog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Blog query()
 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereTag($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereUpdatedAt($value)
 */
	class Blog extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ChatStatus
 *
 * @property int $id
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|ChatStatus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ChatStatus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ChatStatus query()
 * @method static \Illuminate\Database\Eloquent\Builder|ChatStatus whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatStatus whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatStatus whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatStatus whereUpdatedAt($value)
 */
	class ChatStatus extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Enquiry
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $phone
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Enquiry newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Enquiry newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Enquiry query()
 * @method static \Illuminate\Database\Eloquent\Builder|Enquiry whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Enquiry whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Enquiry whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Enquiry whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Enquiry wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Enquiry whereUpdatedAt($value)
 */
	class Enquiry extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Fno
 *
 * @property int $id
 * @property string $title
 * @property string $amount
 * @property string $file
 * @property mixed $json
 * @property string $image
 * @property string $type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Fno newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Fno newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Fno query()
 * @method static \Illuminate\Database\Eloquent\Builder|Fno whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Fno whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Fno whereFile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Fno whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Fno whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Fno whereJson($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Fno whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Fno whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Fno whereUpdatedAt($value)
 */
	class Fno extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Galleries
 *
 * @property int $id
 * @property string $image
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Galleries newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Galleries newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Galleries query()
 * @method static \Illuminate\Database\Eloquent\Builder|Galleries whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Galleries whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Galleries whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Galleries whereUpdatedAt($value)
 */
	class Galleries extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Host
 *
 * @property int $id
 * @property string $name
 * @property string $image
 * @property string $designation
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Workshop[] $workshops
 * @property-read int|null $workshops_count
 * @method static \Illuminate\Database\Eloquent\Builder|Host newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Host newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Host query()
 * @method static \Illuminate\Database\Eloquent\Builder|Host whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Host whereDesignation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Host whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Host whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Host whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Host whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Host whereUpdatedAt($value)
 */
	class Host extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Message
 *
 * @property int $id
 * @property int $from
 * @property string|null $message
 * @property int|null $replied_to
 * @property string|null $attachment
 * @property string $seen
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AppUser $app_user
 * @property-read Message|null $reply
 * @method static \Illuminate\Database\Eloquent\Builder|Message newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Message newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Message query()
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereAttachment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereFrom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereRepliedTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereSeen($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereUpdatedAt($value)
 */
	class Message extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Order
 *
 * @property int $id
 * @property string $order_id
 * @property string $type
 * @property int $app_user_id
 * @property mixed|null $workshop
 * @property mixed|null $package
 * @property string|null $wallet_used
 * @property mixed|null $form_data
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Order newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Order newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Order query()
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereAppUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereFormData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order wherePackage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereWalletUsed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereWorkshop($value)
 */
	class Order extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Package
 *
 * @property int $id
 * @property string $title
 * @property float $amount
 * @property float $service_fees
 * @property int $duration
 * @property string $description
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Package newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Package newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Package query()
 * @method static \Illuminate\Database\Eloquent\Builder|Package whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Package whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Package whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Package whereDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Package whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Package whereServiceFees($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Package whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Package whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Package whereUpdatedAt($value)
 */
	class Package extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Service
 *
 * @property int $id
 * @property string $heading
 * @property string $title
 * @property string $description
 * @property string $image1
 * @property string $image2
 * @property string $image3
 * @property string $pills
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ServiceCard[] $cards
 * @property-read int|null $cards_count
 * @method static \Illuminate\Database\Eloquent\Builder|Service newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Service newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Service query()
 * @method static \Illuminate\Database\Eloquent\Builder|Service whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Service whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Service whereHeading($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Service whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Service whereImage1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Service whereImage2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Service whereImage3($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Service wherePills($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Service whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Service whereUpdatedAt($value)
 */
	class Service extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ServiceCard
 *
 * @property int $id
 * @property int $service_id
 * @property string $title
 * @property string $description
 * @property string $image
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceCard newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceCard newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceCard query()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceCard whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceCard whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceCard whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceCard whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceCard whereServiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceCard whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceCard whereUpdatedAt($value)
 */
	class ServiceCard extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Setting
 *
 * @property int $id
 * @property string $key
 * @property string $value
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Setting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Setting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Setting query()
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereValue($value)
 */
	class Setting extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Slider
 *
 * @property int $id
 * @property string $title
 * @property string $image
 * @property string|null $link
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Slider newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Slider newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Slider query()
 * @method static \Illuminate\Database\Eloquent\Builder|Slider whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Slider whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Slider whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Slider whereLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Slider whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Slider whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Slider whereUpdatedAt($value)
 */
	class Slider extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\SubscriptionTransaction
 *
 * @property int $id
 * @property int $app_user_id
 * @property string $transaction_id
 * @property mixed $package
 * @property int $order_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionTransaction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionTransaction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionTransaction query()
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionTransaction whereAppUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionTransaction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionTransaction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionTransaction whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionTransaction wherePackage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionTransaction whereTransactionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionTransaction whereUpdatedAt($value)
 */
	class SubscriptionTransaction extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Team
 *
 * @property int $id
 * @property string $name
 * @property string $designation
 * @property string $image
 * @property string|null $description
 * @property string|null $facebook
 * @property string|null $twitter
 * @property string|null $linkedin
 * @property string|null $telegram
 * @property string $type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Team newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Team newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Team query()
 * @method static \Illuminate\Database\Eloquent\Builder|Team whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Team whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Team whereDesignation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Team whereFacebook($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Team whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Team whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Team whereLinkedin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Team whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Team whereTelegram($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Team whereTwitter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Team whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Team whereUpdatedAt($value)
 */
	class Team extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\User
 *
 * @property int $id
 * @property string|null $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $username
 * @property string $avatar
 * @property string $messenger_color
 * @property int $dark_mode
 * @property int $active_status
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Laravel\Sanctum\PersonalAccessToken[] $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\UserFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereActiveStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereDarkMode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereMessengerColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUsername($value)
 */
	class User extends \Eloquent implements \Illuminate\Contracts\Auth\MustVerifyEmail {}
}

namespace App\Models{
/**
 * App\Models\Video
 *
 * @property int $id
 * @property string|null $url
 * @property string $description
 * @property string $title
 * @property string|null $video
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Workshop[] $workshops
 * @property-read int|null $workshops_count
 * @method static \Illuminate\Database\Eloquent\Builder|Video newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Video newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Video query()
 * @method static \Illuminate\Database\Eloquent\Builder|Video whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Video whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Video whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Video whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Video whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Video whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Video whereUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Video whereVideo($value)
 */
	class Video extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\WalletTransaction
 *
 * @property int $id
 * @property string $type
 * @property int $workshop_registration_id
 * @property int $subscription_transaction_id
 * @property int $app_user_id
 * @property string $source
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\SubscriptionTransaction $subscription
 * @property-read \App\Models\AppUser $user
 * @property-read \App\Models\WorkshopTransaction $workshop
 * @method static \Illuminate\Database\Eloquent\Builder|WalletTransaction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WalletTransaction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WalletTransaction query()
 * @method static \Illuminate\Database\Eloquent\Builder|WalletTransaction whereAppUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WalletTransaction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WalletTransaction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WalletTransaction whereSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WalletTransaction whereSubscriptionTransactionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WalletTransaction whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WalletTransaction whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WalletTransaction whereWorkshopRegistrationId($value)
 */
	class WalletTransaction extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Workshop
 *
 * @property int $id
 * @property string $title
 * @property string $description
 * @property string $type
 * @property string $workshop_time
 * @property int|null $video_id
 * @property string|null $mail_data
 * @property string $price_type
 * @property float $price
 * @property float $service_fees
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $host_id
 * @property string $image
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Host[] $hosts
 * @property-read int|null $hosts_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\WorkshopRegistration[] $registration
 * @property-read int|null $registration_count
 * @property-read \App\Models\Video|null $video
 * @method static \Illuminate\Database\Eloquent\Builder|Workshop newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Workshop newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Workshop query()
 * @method static \Illuminate\Database\Eloquent\Builder|Workshop whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Workshop whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Workshop whereHostId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Workshop whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Workshop whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Workshop whereMailData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Workshop wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Workshop wherePriceType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Workshop whereServiceFees($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Workshop whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Workshop whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Workshop whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Workshop whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Workshop whereVideoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Workshop whereWorkshopTime($value)
 */
	class Workshop extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\WorkshopRegistration
 *
 * @property int $id
 * @property int $app_user_id
 * @property int $workshop_id
 * @property mixed|null $form_data
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AppUser $users
 * @property-read \App\Models\Workshop $workshops
 * @method static \Illuminate\Database\Eloquent\Builder|WorkshopRegistration newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkshopRegistration newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkshopRegistration query()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkshopRegistration whereAppUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkshopRegistration whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkshopRegistration whereFormData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkshopRegistration whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkshopRegistration whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkshopRegistration whereWorkshopId($value)
 */
	class WorkshopRegistration extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\WorkshopTransaction
 *
 * @property int $id
 * @property mixed $workshop
 * @property int $app_user_id
 * @property int $order_id
 * @property string $transaction_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|WorkshopTransaction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkshopTransaction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkshopTransaction query()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkshopTransaction whereAppUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkshopTransaction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkshopTransaction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkshopTransaction whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkshopTransaction whereTransactionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkshopTransaction whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkshopTransaction whereWorkshop($value)
 */
	class WorkshopTransaction extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\referralPercent
 *
 * @property int $id
 * @property int $percent
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|referralPercent newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|referralPercent newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|referralPercent query()
 * @method static \Illuminate\Database\Eloquent\Builder|referralPercent whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|referralPercent whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|referralPercent wherePercent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|referralPercent whereUpdatedAt($value)
 */
	class referralPercent extends \Eloquent {}
}


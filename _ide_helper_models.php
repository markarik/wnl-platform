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
 * App\Models\PaymentMethod
 *
 * @property int $id
 * @property string $slug
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PaymentMethod newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PaymentMethod newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PaymentMethod query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PaymentMethod whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PaymentMethod whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PaymentMethod whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PaymentMethod whereUpdatedAt($value)
 */
	class PaymentMethod extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ExamResults
 *
 * @property int $id
 * @property int $user_id
 * @property int $exam_tag_id
 * @property int $total
 * @property int $correct
 * @property float $correct_percentage
 * @property int $resolved
 * @property float $resolved_percentage
 * @property mixed|null $details
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ExamResults newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ExamResults newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ExamResults query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ExamResults whereCorrect($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ExamResults whereCorrectPercentage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ExamResults whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ExamResults whereDetails($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ExamResults whereExamTagId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ExamResults whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ExamResults whereResolved($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ExamResults whereResolvedPercentage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ExamResults whereTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ExamResults whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ExamResults whereUserId($value)
 */
	class ExamResults extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Keyword
 *
 * @property int $id
 * @property int $annotation_id
 * @property string $text
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Keyword newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Keyword newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Keyword query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Keyword whereAnnotationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Keyword whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Keyword whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Keyword whereText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Keyword whereUpdatedAt($value)
 */
	class Keyword extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Taxonomy
 *
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $description
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\TagsTaxonomy[] $tagsTaxonomy
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Taxonomy newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Taxonomy newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Taxonomy query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Taxonomy whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Taxonomy whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Taxonomy whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Taxonomy whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Taxonomy whereUpdatedAt($value)
 */
	class Taxonomy extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Category
 *
 * @property int $id
 * @property string $name
 * @property int|null $parent_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Slide[] $slides
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Category newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Category newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Category query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Category whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Category whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Category whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Category whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Category whereUpdatedAt($value)
 */
	class Category extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\QnaAnswer
 *
 * @property int $id
 * @property int $user_id
 * @property int $question_id
 * @property string $text
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Comment[] $comments
 * @property-read \App\Models\QnaQuestion $question
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Reaction[] $reactions
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\QnaAnswer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\QnaAnswer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\QnaAnswer query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\QnaAnswer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\QnaAnswer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\QnaAnswer whereQuestionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\QnaAnswer whereText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\QnaAnswer whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\QnaAnswer whereUserId($value)
 */
	class QnaAnswer extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\UserProfile
 *
 * @property int $id
 * @property int $user_id
 * @property string $first_name
 * @property string $last_name
 * @property string|null $public_email
 * @property string|null $public_phone
 * @property string|null $username
 * @property string|null $avatar
 * @property string|null $city
 * @property string|null $university
 * @property string|null $specialization
 * @property string|null $help
 * @property string|null $interests
 * @property string|null $about
 * @property string|null $learning_location
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $display_name
 * @property string|null $deleted_at
 * @property-read mixed $avatar_url
 * @property-read mixed $full_name
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Role[] $roles
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserProfile newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserProfile newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserProfile query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserProfile whereAbout($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserProfile whereAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserProfile whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserProfile whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserProfile whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserProfile whereDisplayName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserProfile whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserProfile whereHelp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserProfile whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserProfile whereInterests($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserProfile whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserProfile whereLearningLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserProfile wherePublicEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserProfile wherePublicPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserProfile whereSpecialization($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserProfile whereUniversity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserProfile whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserProfile whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserProfile whereUsername($value)
 */
	class UserProfile extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\OrderInstalment
 *
 * @property int $id
 * @property int $order_id
 * @property \Illuminate\Support\Carbon|null $due_date
 * @property float $amount
 * @property float $paid_amount
 * @property int $order_number
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $left_amount
 * @property-read mixed $paid
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInstalment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInstalment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInstalment query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInstalment whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInstalment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInstalment whereDueDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInstalment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInstalment whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInstalment whereOrderNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInstalment wherePaidAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderInstalment whereUpdatedAt($value)
 */
	class OrderInstalment extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Event
 *
 * @property string $id
 * @property string|null $task_id
 * @property array|null $data
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Event newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Event newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Event query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Event whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Event whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Event whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Event whereTaskId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Event whereUpdatedAt($value)
 */
	class Event extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Reaction
 *
 * @property int $id
 * @property string $type
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Reaction count($reactable)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Reaction flags($reactable)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Reaction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Reaction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Reaction query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Reaction type($type)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Reaction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Reaction whereType($value)
 */
	class Reaction extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\SiteWideMessage
 *
 * @property int $id
 * @property string $message
 * @property \Illuminate\Support\Carbon|null $start_date
 * @property \Illuminate\Support\Carbon|null $end_date
 * @property string $target
 * @property string|null $read_at
 * @property int|null $user_id
 * @property string|null $slug
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SiteWideMessage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SiteWideMessage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SiteWideMessage query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SiteWideMessage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SiteWideMessage whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SiteWideMessage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SiteWideMessage whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SiteWideMessage whereReadAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SiteWideMessage whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SiteWideMessage whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SiteWideMessage whereTarget($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SiteWideMessage whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SiteWideMessage whereUserId($value)
 */
	class SiteWideMessage extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Presentable
 *
 * @property int $id
 * @property int $slide_id
 * @property int $presentable_id
 * @property string $presentable_type
 * @property int $order_number
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Presentable newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Presentable newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Presentable query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Presentable whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Presentable whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Presentable whereOrderNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Presentable wherePresentableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Presentable wherePresentableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Presentable whereSlideId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Presentable whereUpdatedAt($value)
 */
	class Presentable extends \Eloquent {}
}

namespace App\Models{
/**
 * Class TaxonomyTerm
 *
 * @package App\Models
 * 
 * NodeTrait Docs: https://github.com/lazychaser/laravel-nestedset
 * @property int $id
 * @property int $_lft
 * @property int $_rgt
 * @property int|null $parent_id
 * @property int $tag_id
 * @property int $taxonomy_id
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Kalnoy\Nestedset\Collection|\App\Models\TaxonomyTerm[] $children
 * @property-read \App\Models\TaxonomyTerm|null $parent
 * @property-read \App\Models\Tag $tag
 * @property-read \App\Models\Taxonomy $taxonomy
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TaxonomyTerm d()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TaxonomyTerm newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TaxonomyTerm newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TaxonomyTerm query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TaxonomyTerm whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TaxonomyTerm whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TaxonomyTerm whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TaxonomyTerm whereLft($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TaxonomyTerm whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TaxonomyTerm whereRgt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TaxonomyTerm whereTagId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TaxonomyTerm whereTaxonomyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TaxonomyTerm whereUpdatedAt($value)
 */
	class TaxonomyTerm extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\UserCourseProgress
 *
 * @property int $id
 * @property int $user_id
 * @property int $lesson_id
 * @property int|null $screen_id
 * @property int|null $section_id
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserCourseProgress newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserCourseProgress newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserCourseProgress query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserCourseProgress whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserCourseProgress whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserCourseProgress whereLessonId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserCourseProgress whereScreenId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserCourseProgress whereSectionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserCourseProgress whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserCourseProgress whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserCourseProgress whereUserId($value)
 */
	class UserCourseProgress extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Group
 *
 * @property int $id
 * @property string $name
 * @property int $course_id
 * @property int $order_number
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Course $course
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Lesson[] $lessons
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Group newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Group newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Group query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Group whereCourseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Group whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Group whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Group whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Group whereOrderNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Group whereUpdatedAt($value)
 */
	class Group extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\PaymentReminder
 *
 * @property int $id
 * @property int $order_id
 * @property int|null $instalment_number
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PaymentReminder newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PaymentReminder newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PaymentReminder query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PaymentReminder whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PaymentReminder whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PaymentReminder whereInstalmentNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PaymentReminder whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PaymentReminder whereUpdatedAt($value)
 */
	class PaymentReminder extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Product
 *
 * @property int $id
 * @property string $name
 * @property string $invoice_name
 * @property string|null $slug
 * @property float $price
 * @property int $quantity
 * @property int $initial
 * @property \Illuminate\Support\Carbon|null $delivery_date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $course_start
 * @property \Illuminate\Support\Carbon|null $course_end
 * @property \Illuminate\Support\Carbon|null $access_start
 * @property \Illuminate\Support\Carbon|null $access_end
 * @property \Illuminate\Support\Carbon|null $signups_start
 * @property \Illuminate\Support\Carbon|null $signups_end
 * @property \Illuminate\Support\Carbon|null $signups_close
 * @property float $vat_rate
 * @property string|null $vat_note
 * @property-read mixed $available
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ProductInstalment[] $instalments
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Lesson[] $lessons
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\PaymentMethod[] $paymentMethods
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product slug($slug)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product whereAccessEnd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product whereAccessStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product whereCourseEnd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product whereCourseStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product whereDeliveryDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product whereInitial($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product whereInvoiceName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product whereSignupsClose($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product whereSignupsEnd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product whereSignupsStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product whereVatNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product whereVatRate($value)
 */
	class Product extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Order
 *
 * @property int $id
 * @property int $user_id
 * @property int $product_id
 * @property string|null $method
 * @property string|null $shipping
 * @property string $shipping_status
 * @property bool $paid
 * @property \Illuminate\Support\Carbon|null $paid_at
 * @property float $paid_amount
 * @property string|null $status
 * @property string|null $session_id
 * @property string|null $external_id
 * @property string|null $transfer_title
 * @property int|null $coupon_id
 * @property bool $invoice
 * @property bool $canceled
 * @property \Illuminate\Support\Carbon|null $canceled_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Coupon|null $coupon
 * @property-read mixed $coupon_amount
 * @property-read mixed $instalments
 * @property-read mixed $is_overdue
 * @property-read mixed $total_with_coupon
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Invoice[] $invoices
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\OrderInstalment[] $orderInstalments
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\PaymentReminder[] $paymentReminders
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Payment[] $payments
 * @property-read \App\Models\Product $product
 * @property-read \App\Models\StudyBuddy $studyBuddy
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order recent()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereCanceled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereCanceledAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereCouponId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereExternalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereInvoice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order wherePaid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order wherePaidAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order wherePaidAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereSessionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereShipping($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereShippingStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereTransferTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereUserId($value)
 */
	class Order extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\UserLesson
 *
 * @property int $id
 * @property int $user_id
 * @property int $lesson_id
 * @property \Illuminate\Support\Carbon|null $start_date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Lesson $lesson
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserLesson newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserLesson newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserLesson query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserLesson whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserLesson whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserLesson whereLessonId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserLesson whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserLesson whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserLesson whereUserId($value)
 */
	class UserLesson extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Session
 *
 * @property int $id
 * @property int $user_id
 * @property string $session_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Session newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Session newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Session query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Session whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Session whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Session whereSessionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Session whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Session whereUserId($value)
 */
	class Session extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Tag
 *
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $description
 * @property string|null $color
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Lesson[] $lessons
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\QnaQuestion[] $questions
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Taggable[] $taggables
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Taxonomy[] $taxonomies
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Tag newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Tag newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Tag query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Tag whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Tag whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Tag whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Tag whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Tag whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Tag whereUpdatedAt($value)
 */
	class Tag extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ChatRoom
 *
 * @property int $id
 * @property string|null $name
 * @property string $type
 * @property string|null $slug
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $is_private
 * @property-read mixed $is_public
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Lesson[] $lessons
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ChatMessage[] $messages
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Permission[] $permissions
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Role[] $roles
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $users
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ChatRoom newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ChatRoom newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ChatRoom ofName($name)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ChatRoom query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ChatRoom slug($slug)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ChatRoom whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ChatRoom whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ChatRoom whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ChatRoom whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ChatRoom whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ChatRoom whereUpdatedAt($value)
 */
	class ChatRoom extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\UserBillingData
 *
 * @property int $id
 * @property int $user_id
 * @property string|null $company_name
 * @property string|null $vat_id
 * @property string|null $address
 * @property string|null $zip
 * @property string|null $city
 * @property string|null $country
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserBillingData newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserBillingData newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserBillingData query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserBillingData whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserBillingData whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserBillingData whereCompanyName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserBillingData whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserBillingData whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserBillingData whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserBillingData whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserBillingData whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserBillingData whereVatId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserBillingData whereZip($value)
 */
	class UserBillingData extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\QuizAnswer
 *
 * @property int $id
 * @property int $quiz_question_id
 * @property string $text
 * @property bool $is_correct
 * @property int $hits
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\QuizAnswer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\QuizAnswer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\QuizAnswer query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\QuizAnswer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\QuizAnswer whereHits($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\QuizAnswer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\QuizAnswer whereIsCorrect($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\QuizAnswer whereQuizQuestionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\QuizAnswer whereText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\QuizAnswer whereUpdatedAt($value)
 */
	class QuizAnswer extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\User
 *
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string $password
 * @property string|null $address
 * @property string|null $zip
 * @property string|null $city
 * @property string|null $phone
 * @property bool $invoice
 * @property string|null $invoice_name
 * @property string|null $invoice_nip
 * @property string|null $invoice_address
 * @property string|null $invoice_zip
 * @property string|null $invoice_city
 * @property string|null $invoice_country
 * @property bool|null $consent_newsletter
 * @property bool|null $consent_account
 * @property bool|null $consent_order
 * @property bool|null $consent_terms
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property bool $suspended
 * @property string|null $deleted_at
 * @property-read \App\Models\UserBillingData $billing
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ChatMessage[] $chatMessages
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ChatRoom[] $chatRooms
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Comment[] $comments
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Coupon[] $coupons
 * @property-read mixed $full_address
 * @property-read mixed $full_name
 * @property-read mixed $identity_number
 * @property-read array $identity_numbers
 * @property-read mixed $is_subscriber
 * @property-read mixed $recipient
 * @property-read mixed $subscription_dates
 * @property-read mixed $subscription_status
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Lesson[] $lessonsAvailability
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Notification[] $notifications
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Order[] $orders
 * @property-read \App\Models\UserPersonalData $personalData
 * @property-read \App\Models\UserProfile $profile
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\QnaAnswer[] $qnaAnswers
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Reactable[] $reactables
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Role[] $roles
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Session[] $sessions
 * @property-read \App\Models\UserSettings $settings
 * @property-read \App\Models\UserSubscription $subscription
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Task[] $tasks
 * @property-read \App\Models\UserAddress $userAddress
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\UserTime[] $userTime
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User ofRole($role)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereConsentAccount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereConsentNewsletter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereConsentOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereConsentTerms($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereInvoice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereInvoiceAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereInvoiceCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereInvoiceCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereInvoiceName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereInvoiceNip($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereInvoiceZip($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereSuspended($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereZip($value)
 */
	class User extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Role
 *
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Role byName($name)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Role newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Role newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Role query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Role whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Role whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Role whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Role whereUpdatedAt($value)
 */
	class Role extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\UserPlan
 *
 * @property int $id
 * @property int $user_id
 * @property int $slack_days_planned
 * @property int $slack_days_left
 * @property array|null $filters
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon|null $start_date
 * @property \Illuminate\Support\Carbon|null $end_date
 * @property-read mixed $stats
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\UserPlanProgress[] $questionsProgress
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserPlan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserPlan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserPlan query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserPlan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserPlan whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserPlan whereFilters($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserPlan whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserPlan whereSlackDaysLeft($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserPlan whereSlackDaysPlanned($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserPlan whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserPlan whereUserId($value)
 */
	class UserPlan extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Lesson
 *
 * @property int $id
 * @property string $name
 * @property int $group_id
 * @property int $order_number
 * @property int $is_required
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\FlashcardsSet $flashcardsSets
 * @property-read mixed $questions
 * @property-read \App\Models\Group $group
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Screen[] $screens
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Tag[] $tags
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Lesson newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Lesson newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Lesson query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Lesson whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Lesson whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Lesson whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Lesson whereIsRequired($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Lesson whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Lesson whereOrderNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Lesson whereUpdatedAt($value)
 */
	class Lesson extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Section
 *
 * @property int $id
 * @property string $name
 * @property int $screen_id
 * @property int $order_number
 * @property int|null $first_slide
 * @property int|null $slides_count
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Screen $screen
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Slide[] $slides
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Subsection[] $subsections
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Tag[] $tags
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Section newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Section newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Section query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Section whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Section whereFirstSlide($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Section whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Section whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Section whereOrderNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Section whereScreenId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Section whereSlidesCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Section whereUpdatedAt($value)
 */
	class Section extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Discussion
 *
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Page $page
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\QnaQuestion[] $questions
 * @property-read \App\Models\Screen $screen
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Discussion newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Discussion newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Discussion query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Discussion whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Discussion whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Discussion whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Discussion whereUpdatedAt($value)
 */
	class Discussion extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\LessonUserAccess
 *
 * @property int $id
 * @property int $lesson_id
 * @property int $user_id
 * @property bool $access
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LessonUserAccess newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LessonUserAccess newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LessonUserAccess query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LessonUserAccess whereAccess($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LessonUserAccess whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LessonUserAccess whereLessonId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LessonUserAccess whereUserId($value)
 */
	class LessonUserAccess extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Task
 *
 * @property mixed $id
 * @property int|null $notifiable_id
 * @property string|null $notifiable_type
 * @property string|null $team
 * @property string|null $subject_type
 * @property int|null $subject_id
 * @property int|null $creator_id
 * @property int|null $assignee_id
 * @property int|null $priority
 * @property int|null $order
 * @property string $status
 * @property string|null $text
 * @property array|null $labels
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $assignee
 * @property-read \App\Models\UserProfile|null $assigneeProfiles
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Comment[] $comments
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Event[] $events
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Reaction[] $reactions
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Tag[] $tags
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Task newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Task newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Task query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Task whereAssigneeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Task whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Task whereCreatorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Task whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Task whereLabels($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Task whereNotifiableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Task whereNotifiableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Task whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Task wherePriority($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Task whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Task whereSubjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Task whereSubjectType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Task whereTeam($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Task whereText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Task whereUpdatedAt($value)
 */
	class Task extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Payment
 *
 * @property int $id
 * @property string $status
 * @property int|null $external_id
 * @property int $order_id
 * @property float|null $amount
 * @property string|null $session_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Order $order
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Payment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Payment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Payment query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Payment whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Payment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Payment whereExternalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Payment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Payment whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Payment whereSessionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Payment whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Payment whereUpdatedAt($value)
 */
	class Payment extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\QnaQuestion
 *
 * @property int $id
 * @property int $user_id
 * @property string $text
 * @property array|null $meta
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int $discussion_id
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\QnaAnswer[] $answers
 * @property-read \App\Models\Discussion $discussion
 * @property-read mixed $page
 * @property-read mixed $screen
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Reaction[] $reactions
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Tag[] $tags
 * @property-read \App\Models\User $user
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\QnaQuestion newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\QnaQuestion newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\QnaQuestion onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\QnaQuestion query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\QnaQuestion whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\QnaQuestion whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\QnaQuestion whereDiscussionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\QnaQuestion whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\QnaQuestion whereMeta($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\QnaQuestion whereText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\QnaQuestion whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\QnaQuestion whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\QnaQuestion withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\QnaQuestion withoutTrashed()
 */
	class QnaQuestion extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Annotation
 *
 * @property int $id
 * @property string|null $title
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Keyword[] $keywords
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Tag[] $tags
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Annotation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Annotation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Annotation query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Annotation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Annotation whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Annotation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Annotation whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Annotation whereUpdatedAt($value)
 */
	class Annotation extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\UserPersonalData
 *
 * @property int $id
 * @property int $user_id
 * @property string|null $personal_identity_number
 * @property string|null $identity_card_number
 * @property string|null $passport_number
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserPersonalData newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserPersonalData newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserPersonalData query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserPersonalData whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserPersonalData whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserPersonalData whereIdentityCardNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserPersonalData wherePassportNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserPersonalData wherePersonalIdentityNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserPersonalData whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserPersonalData whereUserId($value)
 */
	class UserPersonalData extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\PaymentMethodProduct
 *
 * @property int $id
 * @property int $product_id
 * @property int $payment_method_id
 * @property string|null $start_date
 * @property string|null $end_date
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PaymentMethodProduct newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PaymentMethodProduct newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PaymentMethodProduct query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PaymentMethodProduct whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PaymentMethodProduct whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PaymentMethodProduct wherePaymentMethodId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PaymentMethodProduct whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PaymentMethodProduct whereStartDate($value)
 */
	class PaymentMethodProduct extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\FlashcardsSet
 *
 * @property int $id
 * @property string|null $description
 * @property string|null $mind_maps_text
 * @property string $name
 * @property int $lesson_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Flashcard[] $flashcards
 * @property-read \App\Models\Lesson $lesson
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FlashcardsSet newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FlashcardsSet newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FlashcardsSet query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FlashcardsSet whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FlashcardsSet whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FlashcardsSet whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FlashcardsSet whereLessonId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FlashcardsSet whereMindMapsText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FlashcardsSet whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FlashcardsSet whereUpdatedAt($value)
 */
	class FlashcardsSet extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Screen
 *
 * @property int $id
 * @property int $lesson_id
 * @property string $name
 * @property string|null $content
 * @property int $order_number
 * @property string $type
 * @property array|null $meta
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $is_discussable
 * @property int|null $discussion_id
 * @property-read \App\Models\Discussion|null $discussion
 * @property-read mixed $slideshow
 * @property-read \App\Models\Lesson $lesson
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Section[] $sections
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Tag[] $tags
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Screen newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Screen newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Screen query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Screen whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Screen whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Screen whereDiscussionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Screen whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Screen whereIsDiscussable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Screen whereLessonId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Screen whereMeta($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Screen whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Screen whereOrderNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Screen whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Screen whereUpdatedAt($value)
 */
	class Screen extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ChatRoomUser
 *
 * @property int $user_id
 * @property int $chat_room_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $unread_count
 * @property int $id
 * @property int|null $log_pointer
 * @property-read \App\Models\ChatRoom $room
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ChatRoomUser newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ChatRoomUser newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ChatRoomUser query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ChatRoomUser whereChatRoomId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ChatRoomUser whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ChatRoomUser whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ChatRoomUser whereLogPointer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ChatRoomUser whereUnreadCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ChatRoomUser whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ChatRoomUser whereUserId($value)
 */
	class ChatRoomUser extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Structure
 *
 * @property int $id
 * @property string $name
 * @property int $course_id
 * @property int|null $parent_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Course $course
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Screen[] $screens
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Section[] $sections
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Structure newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Structure newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Structure query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Structure whereCourseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Structure whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Structure whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Structure whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Structure whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Structure whereUpdatedAt($value)
 */
	class Structure extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\LessonProduct
 *
 * @property int $lesson_id
 * @property int $product_id
 * @property \Illuminate\Support\Carbon|null $start_date
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LessonProduct newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LessonProduct newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LessonProduct query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LessonProduct whereLessonId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LessonProduct whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LessonProduct whereStartDate($value)
 */
	class LessonProduct extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Invoice
 *
 * @property int $id
 * @property int $order_id
 * @property int $number
 * @property string $series
 * @property string $type
 * @property string $vat
 * @property float $amount
 * @property int|null $corrected_invoice_id
 * @property array|null $meta
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Invoice[] $correctives
 * @property-read mixed $corrected_amount
 * @property-read mixed $file_name
 * @property-read mixed $file_path
 * @property-read mixed $full_number
 * @property-read mixed $net_value
 * @property-read mixed $number_slugged
 * @property-read mixed $vat_amount
 * @property-read mixed $vat_rate
 * @property-read \App\Models\Order $order
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Invoice newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Invoice newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Invoice query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Invoice recent()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Invoice whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Invoice whereCorrectedInvoiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Invoice whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Invoice whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Invoice whereMeta($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Invoice whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Invoice whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Invoice whereSeries($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Invoice whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Invoice whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Invoice whereVat($value)
 */
	class Invoice extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\QuizSet
 *
 * @property int $id
 * @property string|null $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\QuizQuestion[] $questions
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\QuizSet newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\QuizSet newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\QuizSet query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\QuizSet whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\QuizSet whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\QuizSet whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\QuizSet whereUpdatedAt($value)
 */
	class QuizSet extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Course
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Group[] $groups
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Course newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Course newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Course query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Course whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Course whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Course whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Course whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Course whereUpdatedAt($value)
 */
	class Course extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\UserSubscription
 *
 * @property int $id
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $access_start
 * @property \Illuminate\Support\Carbon|null $access_end
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserSubscription newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserSubscription newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserSubscription query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserSubscription whereAccessEnd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserSubscription whereAccessStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserSubscription whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserSubscription whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserSubscription whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserSubscription whereUserId($value)
 */
	class UserSubscription extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Slideshow
 *
 * @property int $id
 * @property string|null $background
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $background_url
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Slide[] $slides
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Slideshow newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Slideshow newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Slideshow query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Slideshow whereBackground($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Slideshow whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Slideshow whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Slideshow whereUpdatedAt($value)
 */
	class Slideshow extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\UserTime
 *
 * @property int $id
 * @property int $user_id
 * @property int $time
 * @property string|null $created_at
 * @property string|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserTime newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserTime newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserTime query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserTime whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserTime whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserTime whereTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserTime whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserTime whereUserId($value)
 */
	class UserTime extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\UserSettings
 *
 * @property int $id
 * @property int $user_id
 * @property array|null $settings
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserSettings newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserSettings newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserSettings query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserSettings whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserSettings whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserSettings whereSettings($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserSettings whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserSettings whereUserId($value)
 */
	class UserSettings extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\UserFlashcardsResults
 *
 * @property int $id
 * @property int $user_id
 * @property int $flashcard_id
 * @property string $answer
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $context_id
 * @property string|null $context_type
 * @property-read \App\Models\Flashcard $flashcard
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserFlashcardsResults newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserFlashcardsResults newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserFlashcardsResults query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserFlashcardsResults whereAnswer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserFlashcardsResults whereContextId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserFlashcardsResults whereContextType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserFlashcardsResults whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserFlashcardsResults whereFlashcardId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserFlashcardsResults whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserFlashcardsResults whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserFlashcardsResults whereUserId($value)
 */
	class UserFlashcardsResults extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Comment
 *
 * @property int $id
 * @property int $user_id
 * @property string $text
 * @property int $commentable_id
 * @property string $commentable_type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $commentable
 * @property-read \App\Models\UserProfile $profiles
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Reaction[] $reactions
 * @property-read \App\Models\User $user
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Comment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Comment newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Comment onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Comment query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Comment whereCommentableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Comment whereCommentableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Comment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Comment whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Comment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Comment whereText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Comment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Comment whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Comment withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Comment withoutTrashed()
 */
	class Comment extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Reactable
 *
 * @property int $id
 * @property int $user_id
 * @property int $reaction_id
 * @property int $reactable_id
 * @property string $reactable_type
 * @property mixed|null $context
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Reactable newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Reactable newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Reactable query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Reactable whereContext($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Reactable whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Reactable whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Reactable whereReactableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Reactable whereReactableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Reactable whereReactionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Reactable whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Reactable whereUserId($value)
 */
	class Reactable extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\UserPlanProgress
 *
 * @property int $id
 * @property int $plan_id
 * @property int $user_id
 * @property int $question_id
 * @property string|null $resolved_at
 * @property-read \App\Models\UserPlan $plan
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserPlanProgress newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserPlanProgress newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserPlanProgress query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserPlanProgress whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserPlanProgress wherePlanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserPlanProgress whereQuestionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserPlanProgress whereResolvedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserPlanProgress whereUserId($value)
 */
	class UserPlanProgress extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Subscriber
 *
 * @property int $id
 * @property string $email
 * @property string|null $first_name
 * @property string|null $last_name
 * @property string|null $subscribed_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Subscriber newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Subscriber newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Subscriber query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Subscriber whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Subscriber whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Subscriber whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Subscriber whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Subscriber whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Subscriber whereSubscribedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Subscriber whereUpdatedAt($value)
 */
	class Subscriber extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\StudyBuddy
 *
 * @property int $id
 * @property int $order_id
 * @property string $code
 * @property string|null $recipient
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Coupon $coupon
 * @property-read \App\Models\Order $order
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StudyBuddy newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StudyBuddy newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StudyBuddy query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StudyBuddy whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StudyBuddy whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StudyBuddy whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StudyBuddy whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StudyBuddy whereRecipient($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StudyBuddy whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StudyBuddy whereUpdatedAt($value)
 */
	class StudyBuddy extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\QuizQuestion
 *
 * @property int $id
 * @property string $text
 * @property string|null $explanation
 * @property bool $preserve_order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\QuizAnswer[] $answers
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Comment[] $comments
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Reaction[] $reactions
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\QuizSet[] $sets
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Slide[] $slides
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Tag[] $tags
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\UserQuizResults[] $userQuizResults
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\QuizQuestion newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\QuizQuestion newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\QuizQuestion onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\QuizQuestion query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\QuizQuestion whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\QuizQuestion whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\QuizQuestion whereExplanation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\QuizQuestion whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\QuizQuestion wherePreserveOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\QuizQuestion whereText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\QuizQuestion whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\QuizQuestion withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\QuizQuestion withoutTrashed()
 */
	class QuizQuestion extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\UserQuizResults
 *
 * @property int $quiz_question_id
 * @property int $quiz_answer_id
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $id
 * @property-read \App\Models\QuizAnswer $quizAnswer
 * @property-read \App\Models\QuizQuestion $quizQuestion
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserQuizResults newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserQuizResults newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserQuizResults query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserQuizResults whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserQuizResults whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserQuizResults whereQuizAnswerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserQuizResults whereQuizQuestionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserQuizResults whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserQuizResults whereUserId($value)
 */
	class UserQuizResults extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\TagsTaxonomy
 *
 * @property int $id
 * @property int $tag_id
 * @property int|null $parent_tag_id
 * @property int $taxonomy_id
 * @property int $order_number
 * @property-read \App\Models\Tag|null $parentTag
 * @property-read \App\Models\Tag $tag
 * @property-read \App\Models\Taxonomy $taxonomy
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TagsTaxonomy newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TagsTaxonomy newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TagsTaxonomy query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TagsTaxonomy whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TagsTaxonomy whereOrderNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TagsTaxonomy whereParentTagId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TagsTaxonomy whereTagId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TagsTaxonomy whereTaxonomyId($value)
 */
	class TagsTaxonomy extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Page
 *
 * @property int $id
 * @property string|null $content
 * @property string|null $name
 * @property string|null $slug
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $is_discussable
 * @property int|null $discussion_id
 * @property-read \App\Models\Discussion|null $discussion
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Tag[] $tags
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Page newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Page newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Page query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Page whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Page whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Page whereDiscussionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Page whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Page whereIsDiscussable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Page whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Page whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Page whereUpdatedAt($value)
 */
	class Page extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\UserFlashcardNote
 *
 * @property int $id
 * @property int $user_id
 * @property int $flashcard_id
 * @property string $note
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Flashcard $flashcard
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserFlashcardNote newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserFlashcardNote newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserFlashcardNote query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserFlashcardNote whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserFlashcardNote whereFlashcardId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserFlashcardNote whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserFlashcardNote whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserFlashcardNote whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserFlashcardNote whereUserId($value)
 */
	class UserFlashcardNote extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ProductInstalment
 *
 * @property int $id
 * @property int $product_id
 * @property string $value_type
 * @property float $value
 * @property \Illuminate\Support\Carbon|null $due_date
 * @property int|null $due_days
 * @property int $order_number
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductInstalment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductInstalment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductInstalment query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductInstalment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductInstalment whereDueDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductInstalment whereDueDays($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductInstalment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductInstalment whereOrderNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductInstalment whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductInstalment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductInstalment whereValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductInstalment whereValueType($value)
 */
	class ProductInstalment extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Notification
 *
 * @property string $id
 * @property string $type
 * @property int $notifiable_id
 * @property string $notifiable_type
 * @property array $data
 * @property string|null $channel
 * @property \Illuminate\Support\Carbon|null $read_at
 * @property \Illuminate\Support\Carbon|null $seen_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $event_id
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Notification newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Notification newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Notification query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Notification whereChannel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Notification whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Notification whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Notification whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Notification whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Notification whereNotifiableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Notification whereNotifiableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Notification whereReadAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Notification whereSeenAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Notification whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Notification whereUpdatedAt($value)
 */
	class Notification extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Slide
 *
 * @property int $id
 * @property string $content
 * @property array|null $snippet
 * @property bool $is_functional
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Category[] $categories
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Comment[] $comments
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\QuizQuestion[] $quizQuestions
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Reaction[] $reactions
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Section[] $sections
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Slideshow[] $slideshow
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Subsection[] $subsections
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Tag[] $tags
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Slide newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Slide newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Slide query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Slide whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Slide whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Slide whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Slide whereIsFunctional($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Slide whereSnippet($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Slide whereUpdatedAt($value)
 */
	class Slide extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Coupon
 *
 * @property int $id
 * @property int|null $user_id
 * @property string|null $name
 * @property string|null $code
 * @property string $type
 * @property float $value
 * @property string|null $slug
 * @property \Illuminate\Support\Carbon|null $expires_at
 * @property int|null $times_usable
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $is_percentage
 * @property-read mixed $value_with_unit
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Product[] $products
 * @property-read \App\Models\StudyBuddy $studyBuddy
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Coupon newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Coupon newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Coupon query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Coupon slug($slug)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Coupon validCode($code)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Coupon whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Coupon whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Coupon whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Coupon whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Coupon whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Coupon whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Coupon whereTimesUsable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Coupon whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Coupon whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Coupon whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Coupon whereValue($value)
 */
	class Coupon extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Permission
 *
 * @property int $id
 * @property string $slug
 * @property string|null $name
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ChatRoom[] $chatRooms
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Permission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Permission newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Permission query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Permission slug($slug)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Permission whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Permission whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Permission whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Permission whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Permission whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Permission whereUpdatedAt($value)
 */
	class Permission extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ChatMessage
 *
 * @property int $id
 * @property string|null $content
 * @property int|null $user_id
 * @property int|null $chat_room_id
 * @property int $time
 * @property-read \App\Models\ChatRoom|null $chatRoom
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ChatMessage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ChatMessage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ChatMessage query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ChatMessage whereChatRoomId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ChatMessage whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ChatMessage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ChatMessage whereTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ChatMessage whereUserId($value)
 */
	class ChatMessage extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Taggable
 *
 * @property int $id
 * @property int $tag_id
 * @property int $taggable_id
 * @property string $taggable_type
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Taggable newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Taggable newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Taggable query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Taggable whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Taggable whereTagId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Taggable whereTaggableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Taggable whereTaggableType($value)
 */
	class Taggable extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Subsection
 *
 * @property int $id
 * @property string $name
 * @property int $section_id
 * @property int $order_number
 * @property int|null $first_slide
 * @property int|null $slides_count
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Section $section
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Slide[] $slides
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Tag[] $tags
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Subsection newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Subsection newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Subsection query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Subsection whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Subsection whereFirstSlide($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Subsection whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Subsection whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Subsection whereOrderNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Subsection whereSectionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Subsection whereSlidesCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Subsection whereUpdatedAt($value)
 */
	class Subsection extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\UserAddress
 *
 * @property int $id
 * @property int $user_id
 * @property string|null $street
 * @property string|null $zip
 * @property string|null $city
 * @property string|null $phone
 * @property string|null $recipient
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserAddress newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserAddress newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserAddress query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserAddress whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserAddress whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserAddress whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserAddress wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserAddress whereRecipient($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserAddress whereStreet($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserAddress whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserAddress whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserAddress whereZip($value)
 */
	class UserAddress extends \Eloquent {}
}


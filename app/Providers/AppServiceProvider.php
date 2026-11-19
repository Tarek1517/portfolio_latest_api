<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);

        Builder::macro('search', function ($attributes, $searchTerm) {
            /** @disregard [OPTIONAL CODE] [OPTIONAL DESCRIPTION] */
            return $this->where(function (Builder $query) use ($attributes, $searchTerm) {
                foreach (Arr::wrap($attributes) as $attribute) {
                    $query->when(
                        ! ($attribute instanceof \Illuminate\Contracts\Database\Query\Expression) && str_contains((string) $attribute, '.'),
                        function (Builder $query) use ($attribute, $searchTerm) {
                            $attrs = explode('.', (string) $attribute);
                            $relatedAttribute = array_pop($attrs);
                            $relation = implode('.', $attrs);
                            $query->orWhereRelation($relation, $relatedAttribute, 'LIKE', "%{$searchTerm}%");
                        },
                        function (Builder $query) use ($attribute, $searchTerm) {
                            $query->orWhere($attribute, 'LIKE', "%{$searchTerm}%");
                        }
                    );
                }
            })
                ->latest()
                ->when(
                    request()->filled('pagination') && request()->pagination === 'off',
                    fn($query) => $query->get(),
                    fn($query) => $query->paginate(request()->per_page ?? 40)
                );
        });
        Validator::extend('phone', function ($attribute, $value, $parameters, $validator) {
            return preg_match('/^01[3-9]\d{8}$/', $value);
        }, 'The :attribute must be a valid Bangladeshi phone number.');
    }
}

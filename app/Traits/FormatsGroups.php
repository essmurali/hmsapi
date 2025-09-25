<?php

namespace App\Traits;

trait FormatsGroups
{
    /**
     * Recursively formats the category collection into the desired array structure.
     *
     * @param \Illuminate\Support\Collection $categories
     * @return array
     */
    protected function formatGroups($categories): array
    {
        $result = [];
        foreach ($categories as $category) {
            $name = $category->name;

            if ($category->children->isNotEmpty()) {
                $result[] = [
                    $name => $this->formatGroups($category->children)
                ];
            } else {
                $result[] = $name;
            }
        }
        return $result;
    }
}

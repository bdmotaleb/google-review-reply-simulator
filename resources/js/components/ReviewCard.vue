<template>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md hover:shadow-xl transition-all duration-300 border border-gray-100 dark:border-gray-700 p-6 mb-6 transform hover:-translate-y-1 cursor-default">
        <div class="flex items-start justify-between mb-4">
            <div class="flex-1">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2 transition-colors duration-200">
                    {{ review.reviewer_name }}
                </h3>
                <div class="flex items-center space-x-2 mb-3">
                    <!-- Star Rating Display -->
                    <div class="flex items-center space-x-0.5">
                        <span v-for="star in 5" :key="star" class="transition-all duration-200">
                            <svg
                                :class="[
                                    'w-5 h-5 transition-all duration-200',
                                    star <= review.rating
                                        ? 'text-yellow-400 fill-current drop-shadow-sm'
                                        : 'text-gray-300 dark:text-gray-600 fill-current'
                                ]"
                                viewBox="0 0 20 20"
                            >
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                        </span>
                    </div>
                    <span class="text-sm font-medium text-gray-600 dark:text-gray-400">
                        {{ review.rating }} / 5
                    </span>
                </div>
            </div>

            <!-- Sentiment Badge -->
            <span
                :class="[
                    'px-3 py-1.5 rounded-full text-xs font-bold capitalize transition-all duration-200 shadow-sm',
                    sentimentClasses[review.sentiment]
                ]"
            >
                {{ review.sentiment }}
            </span>
        </div>

        <!-- Review Text -->
        <p class="text-gray-700 dark:text-gray-300 mb-4 whitespace-pre-wrap leading-relaxed">
            {{ review.review_text }}
        </p>

        <!-- Timestamp -->
        <div class="text-xs text-gray-500 dark:text-gray-400 flex items-center">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            {{ formatDate(review.created_at) }}
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    review: {
        type: Object,
        required: true,
    },
});

const sentimentClasses = {
    positive: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
    neutral: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
    negative: 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
};

const formatDate = (dateString) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};
</script>


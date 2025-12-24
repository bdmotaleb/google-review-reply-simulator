<template>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100 dark:border-gray-700 p-6 md:p-8">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">
            Submit a Review
        </h2>

        <form @submit.prevent="handleSubmit" class="space-y-6">
            <!-- Reviewer Name -->
            <div>
                <label for="reviewer_name" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                    Your Name
                </label>
                <input
                    id="reviewer_name"
                    v-model="form.reviewer_name"
                    type="text"
                    required
                    maxlength="255"
                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-all duration-200 hover:border-gray-400 dark:hover:border-gray-500"
                    placeholder="Enter your name"
                />
            </div>

            <!-- Star Rating -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
                    Rating
                </label>
                <div class="flex items-center space-x-2">
                    <button
                        v-for="star in 5"
                        :key="star"
                        type="button"
                        @click="form.rating = star"
                        class="focus:outline-none transition-all duration-200 hover:scale-125 active:scale-95"
                    >
                        <svg
                            :class="[
                                'w-10 h-10 transition-all duration-300',
                                star <= form.rating
                                    ? 'text-yellow-400 fill-current drop-shadow-sm'
                                    : 'text-gray-300 dark:text-gray-600 fill-current hover:text-yellow-300'
                            ]"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                        >
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                    </button>
                    <span class="ml-3 text-sm font-medium text-gray-600 dark:text-gray-400">
                        {{ form.rating }} / 5
                    </span>
                </div>
            </div>

            <!-- Review Text -->
            <div>
                <label for="review_text" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                    Review Text
                </label>
                <textarea
                    id="review_text"
                    v-model="form.review_text"
                    required
                    rows="6"
                    maxlength="5000"
                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white resize-none transition-all duration-200 hover:border-gray-400 dark:hover:border-gray-500"
                    placeholder="Write your review here..."
                ></textarea>
                <div class="mt-2 text-xs text-gray-500 dark:text-gray-400 text-right">
                    {{ form.review_text.length }} / 5000
                </div>
            </div>

            <!-- Submit Button -->
            <button
                type="submit"
                :disabled="loading"
                class="w-full bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 disabled:from-gray-400 disabled:to-gray-500 disabled:cursor-not-allowed text-white font-semibold py-3.5 px-6 rounded-lg transition-all duration-300 flex items-center justify-center shadow-md hover:shadow-lg transform hover:-translate-y-0.5 disabled:transform-none disabled:shadow-none"
            >
                <span v-if="loading" class="mr-2">
                    <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </span>
                {{ loading ? 'Submitting...' : 'Submit Review' }}
            </button>
        </form>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';

const emit = defineEmits(['review-submitted']);

const form = ref({
    reviewer_name: '',
    rating: 0,
    review_text: '',
});

const loading = ref(false);

const handleSubmit = async () => {
    if (form.value.rating === 0) {
        alert('Please select a rating');
        return;
    }

    loading.value = true;

    try {
        const response = await axios.post('/api/reviews', form.value);
        emit('review-submitted', response.data.data);
        
        // Reset form
        form.value = {
            reviewer_name: '',
            rating: 0,
            review_text: '',
        };
    } catch (error) {
        console.error('Error submitting review:', error);
        alert(error.response?.data?.message || 'Failed to submit review. Please try again.');
    } finally {
        loading.value = false;
    }
};
</script>


<template>
    <div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-gray-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900">
        <div class="max-w-7xl mx-auto py-8 sm:py-12 px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="text-center mb-10 sm:mb-16">
                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-gray-900 dark:text-white mb-4 tracking-tight">
                    Google Review Reply Simulator
                </h1>
                <p class="text-base sm:text-lg text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
                    Generate AI-powered reply suggestions for your reviews
                </p>
            </div>

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 sm:gap-8 mb-10 sm:mb-16">
                <!-- Review Form -->
                <div class="transition-all duration-300">
                    <ReviewForm @review-submitted="handleReviewSubmitted" />
                </div>

                <!-- Reply Suggestions (shown after review submission) -->
                <Transition
                    enter-active-class="transition ease-out duration-500"
                    enter-from-class="opacity-0 translate-y-4"
                    enter-to-class="opacity-100 translate-y-0"
                    leave-active-class="transition ease-in duration-300"
                    leave-from-class="opacity-100 translate-y-0"
                    leave-to-class="opacity-0 translate-y-4"
                >
                    <div v-if="selectedReview" class="transition-all duration-300">
                        <ReplySuggestions 
                            :suggestions="selectedReview.reply_suggestions || []" 
                            :on-copy="showToast"
                        />
                    </div>
                </Transition>
            </div>

            <!-- Reviews List -->
            <Transition
                enter-active-class="transition ease-out duration-500"
                enter-from-class="opacity-0"
                enter-to-class="opacity-100"
            >
                <div v-if="reviews.length > 0" class="mb-8">
                    <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white mb-6 sm:mb-8">
                        All Reviews
                    </h2>
                    <div class="space-y-6 sm:space-y-8">
                        <TransitionGroup
                            enter-active-class="transition ease-out duration-300"
                            enter-from-class="opacity-0 translate-y-4"
                            enter-to-class="opacity-100 translate-y-0"
                            leave-active-class="transition ease-in duration-200"
                            leave-from-class="opacity-100"
                            leave-to-class="opacity-0"
                        >
                            <div
                                v-for="review in reviews"
                                :key="review.id"
                                class="grid grid-cols-1 lg:grid-cols-3 gap-6"
                            >
                                <div class="lg:col-span-2">
                                    <ReviewCard :review="review" />
                                </div>
                                <div>
                                    <ReplySuggestions 
                                        :suggestions="review.reply_suggestions || []" 
                                        :on-copy="showToast"
                                    />
                                </div>
                            </div>
                        </TransitionGroup>
                    </div>
                </div>
            </Transition>

            <!-- Empty State -->
            <Transition
                enter-active-class="transition ease-out duration-500"
                enter-from-class="opacity-0 scale-95"
                enter-to-class="opacity-100 scale-100"
            >
                <div v-if="reviews.length === 0 && !loading" class="text-center py-16 sm:py-24">
                    <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gray-100 dark:bg-gray-800 mb-6">
                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">No reviews yet</h3>
                    <p class="text-sm sm:text-base text-gray-500 dark:text-gray-400 max-w-md mx-auto">
                        Submit your first review to see AI-powered reply suggestions
                    </p>
                </div>
            </Transition>
        </div>

        <!-- Toast Notification -->
        <Toast ref="toastRef" />
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import ReviewForm from './ReviewForm.vue';
import ReviewCard from './ReviewCard.vue';
import ReplySuggestions from './ReplySuggestions.vue';
import Toast from './Toast.vue';

const reviews = ref([]);
const selectedReview = ref(null);
const loading = ref(false);
const toastRef = ref(null);

const loadReviews = async () => {
    loading.value = true;
    try {
        const response = await axios.get('/api/reviews');
        reviews.value = response.data.data;
    } catch (error) {
        console.error('Error loading reviews:', error);
    } finally {
        loading.value = false;
    }
};

const handleReviewSubmitted = (review) => {
    // Add the new review to the beginning of the list
    reviews.value.unshift(review);
    // Set as selected review to show suggestions
    selectedReview.value = review;
    // Scroll to top smoothly
    window.scrollTo({ top: 0, behavior: 'smooth' });
};

const showToast = (message = 'Copied to clipboard!') => {
    if (toastRef.value) {
        toastRef.value.open(message);
    }
};

onMounted(() => {
    loadReviews();
});
</script>


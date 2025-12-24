<template>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md hover:shadow-lg transition-all duration-300 border border-gray-100 dark:border-gray-700 p-6">
        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-5">
            Reply Suggestions
        </h3>

        <div v-if="suggestions && suggestions.length > 0" class="space-y-4">
            <div
                v-for="suggestion in suggestions"
                :key="suggestion.id"
                class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:border-gray-300 dark:hover:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-200 transform hover:scale-[1.01]"
            >
                <div class="flex items-start justify-between mb-3">
                    <span
                        :class="[
                            'px-3 py-1 rounded-full text-xs font-bold capitalize shadow-sm transition-all duration-200',
                            toneClasses[suggestion.tone]
                        ]"
                    >
                        {{ suggestion.tone }}
                    </span>
                    <button
                        @click="copyToClipboard(suggestion.reply_text, suggestion.tone)"
                        class="flex items-center space-x-2 px-4 py-2 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white text-sm font-semibold rounded-lg transition-all duration-200 shadow-md hover:shadow-lg transform hover:scale-105 active:scale-95"
                    >
                        <svg class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                        <span>Copy</span>
                    </button>
                </div>
                <p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap leading-relaxed">
                    {{ suggestion.reply_text }}
                </p>
            </div>
        </div>

        <div v-else class="text-center py-12 text-gray-500 dark:text-gray-400">
            <svg class="mx-auto h-12 w-12 mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
            </svg>
            <p class="text-sm">No reply suggestions available</p>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';

const props = defineProps({
    suggestions: {
        type: Array,
        default: () => [],
    },
    onCopy: {
        type: Function,
        default: null,
    },
});

const toneClasses = {
    friendly: 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
    professional: 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200',
    witty: 'bg-pink-100 text-pink-800 dark:bg-pink-900 dark:text-pink-200',
};

const copyToClipboard = async (text, tone) => {
    try {
        await navigator.clipboard.writeText(text);
        
        // Emit copy event to parent for toast notification
        if (props.onCopy) {
            props.onCopy(`Copied ${tone} reply to clipboard!`);
        }
    } catch (err) {
        console.error('Failed to copy text:', err);
        if (props.onCopy) {
            props.onCopy('Failed to copy to clipboard', 'error');
        }
    }
};
</script>


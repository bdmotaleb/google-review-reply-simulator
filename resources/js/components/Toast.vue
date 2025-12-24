<template>
    <Transition
        enter-active-class="transition ease-out duration-300"
        enter-from-class="opacity-0 translate-y-2 scale-95"
        enter-to-class="opacity-100 translate-y-0 scale-100"
        leave-active-class="transition ease-in duration-200"
        leave-from-class="opacity-100 translate-y-0 scale-100"
        leave-to-class="opacity-0 translate-y-2 scale-95"
    >
        <div
            v-if="show"
            class="fixed bottom-4 right-4 z-50 max-w-sm w-full sm:max-w-md"
        >
            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-gray-200 dark:border-gray-700 p-4 flex items-center space-x-3 backdrop-blur-sm"
            >
                <div class="flex-shrink-0">
                    <svg
                        class="w-6 h-6 text-green-500"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                        />
                    </svg>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">
                        {{ currentMessage }}
                    </p>
                </div>
                <button
                    @click="close"
                    class="flex-shrink-0 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors duration-200 p-1 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </Transition>
</template>

<script setup>
import { ref, onUnmounted } from 'vue';

const props = defineProps({
    duration: {
        type: Number,
        default: 3000,
    },
});

const show = ref(false);
const currentMessage = ref('Copied to clipboard!');
let timeoutId = null;

const close = () => {
    show.value = false;
};

const open = (message = 'Copied to clipboard!') => {
    currentMessage.value = message;
    show.value = true;
    if (timeoutId) {
        clearTimeout(timeoutId);
    }
    timeoutId = setTimeout(() => {
        close();
    }, props.duration);
};

defineExpose({
    open,
    close,
});

onUnmounted(() => {
    if (timeoutId) {
        clearTimeout(timeoutId);
    }
});
</script>


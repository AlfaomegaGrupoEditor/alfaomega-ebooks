import { ref } from 'vue';
import { ToastType } from '@/types';

const toast = ref<ToastType | null>(null);
const toastActive = ref(false);

const showToast = (newToast: ToastType) => {
    toast.value = newToast;
    toastActive.value = true;
    setTimeout(() => {
        toastActive.value = false;
    }, 5000);
};

export function useToast() {
    return {
        toast,
        toastActive,
        showToast
    };
}

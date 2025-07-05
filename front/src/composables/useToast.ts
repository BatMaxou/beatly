import { toast } from "vue3-toastify";
import "vue3-toastify/dist/index.css";

export function useToast() {
  const showSuccess = (message: string, duration = 3000) => {
    toast.success(message, {
      autoClose: duration,
      position: toast.POSITION.TOP_RIGHT,
    });
  };

  const showError = (message: string, duration = 3000) => {
    toast.error(message, {
      autoClose: duration,
      position: toast.POSITION.TOP_RIGHT,
    });
  };

  const showInfo = (message: string, duration = 3000) => {
    toast.info(message, {
      autoClose: duration,
      position: toast.POSITION.TOP_RIGHT,
    });
  };

  const showWarning = (message: string, duration = 3000) => {
    toast.warning(message, {
      autoClose: duration,
      position: toast.POSITION.TOP_RIGHT,
    });
  };

  return {
    showSuccess,
    showError,
    showInfo,
    showWarning,
  };
}

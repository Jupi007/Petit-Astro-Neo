export { };

// error  in /srv/vendor/symfony/ux-turbo/Resources/assets/src/turbo_controller.ts
// [tsl] ERROR in /srv/vendor/symfony/ux-turbo/Resources/assets/src/turbo_controller.ts(14,8)
//       TS2339: Property 'Turbo' does not exist on type 'Window & typeof globalThis'.
declare global {
    interface Window {
        Turbo: any;
        Alpine: any;
    }
}

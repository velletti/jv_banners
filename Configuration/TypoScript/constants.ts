
plugin.tx_jvbanners_connector {
    view {
        # cat=plugin.tx_jvbanners_connector/file; type=string; label=Path to template root (FE)
        templateRootPath = EXT:jv_banners/Resources/Private/Templates/
        # cat=plugin.tx_jvbanners_connector/file; type=string; label=Path to template partials (FE)
        partialRootPath = EXT:jv_banners/Resources/Private/Partials/
        # cat=plugin.tx_jvbanners_connector/file; type=string; label=Path to template layouts (FE)
        layoutRootPath = EXT:jv_banners/Resources/Private/Layouts/
    }
    persistence {
        # cat=plugin.tx_jvbanners_connector//a; type=string; label=Default storage PID
        storagePid =
    }
}

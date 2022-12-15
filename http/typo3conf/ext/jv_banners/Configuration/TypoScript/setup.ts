
plugin.tx_jvbanners_connector {
    view {
        templateRootPaths.0 = EXT:{extension.extensionKey}/Resources/Private/Templates/
        templateRootPaths.1 = {$plugin.tx_jvbanners_connector.view.templateRootPath}
        partialRootPaths.0 = EXT:jv_banners/Resources/Private/Partials/
        partialRootPaths.1 = {$plugin.tx_jvbanners_connector.view.partialRootPath}
        layoutRootPaths.0 = EXT:jv_banners/Resources/Private/Layouts/
        layoutRootPaths.1 = {$plugin.tx_jvbanners_connector.view.layoutRootPath}
    }
    persistence {
        storagePid = {$plugin.tx_jvbanners_connector.persistence.storagePid}
        #recursive = 1
    }
    features {
        skipDefaultArguments = 1
        # if set to 1, the enable fields are ignored in BE context
        ignoreAllEnableFieldsInBe = 0
        # Should be on by default, but can be disabled if all action in the plugin are uncached
        requireCHashArgumentForActionArguments = 0
    }
    mvc {
        callDefaultActionIfActionCantBeResolved = 1
    }
}
config.tx_extbase {
    objects {
        DERHANSEN\SfBanners\Domain\Model\Banner {
            className = JVE\JvBanners\Domain\Model\Banner
        }
    }
}


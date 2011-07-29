
Name: app-multiwan
Group: ClearOS/Apps
Version: 5.9.9.2
Release: 1%{dist}
Summary: Multi-WAN
License: GPLv3
Packager: ClearFoundation
Vendor: ClearFoundation
Source: %{name}-%{version}.tar.gz
Buildarch: noarch
Requires: %{name}-core = %{version}-%{release}
Requires: app-base
Requires: app-network

%description
Translation missing (multiwan_app_long_description)

%package core
Summary: Multi-WAN - APIs and install
Group: ClearOS/Libraries
License: LGPLv3
Requires: app-base-core
Requires: app-network-core
Requires: app-firewall-core
Requires: csplugin-routewatch
Requires: iptables
Requires: syswatch

%description core
Translation missing (multiwan_app_long_description)

This package provides the core API and libraries.

%prep
%setup -q
%build

%install
mkdir -p -m 755 %{buildroot}/usr/clearos/apps/multiwan
cp -r * %{buildroot}/usr/clearos/apps/multiwan/


%post
logger -p local6.notice -t installer 'app-multiwan - installing'

%post core
logger -p local6.notice -t installer 'app-multiwan-core - installing'

if [ $1 -eq 1 ]; then
    [ -x /usr/clearos/apps/multiwan/deploy/install ] && /usr/clearos/apps/multiwan/deploy/install
fi

[ -x /usr/clearos/apps/multiwan/deploy/upgrade ] && /usr/clearos/apps/multiwan/deploy/upgrade

exit 0

%preun
if [ $1 -eq 0 ]; then
    logger -p local6.notice -t installer 'app-multiwan - uninstalling'
fi

%preun core
if [ $1 -eq 0 ]; then
    logger -p local6.notice -t installer 'app-multiwan-core - uninstalling'
    [ -x /usr/clearos/apps/multiwan/deploy/uninstall ] && /usr/clearos/apps/multiwan/deploy/uninstall
fi

exit 0

%files
%defattr(-,root,root)
/usr/clearos/apps/multiwan/controllers
/usr/clearos/apps/multiwan/htdocs
/usr/clearos/apps/multiwan/views

%files core
%defattr(-,root,root)
%exclude /usr/clearos/apps/multiwan/packaging
%exclude /usr/clearos/apps/multiwan/tests
%dir /usr/clearos/apps/multiwan
/usr/clearos/apps/multiwan/deploy
/usr/clearos/apps/multiwan/language
/usr/clearos/apps/multiwan/libraries

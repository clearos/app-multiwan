
Name: app-multiwan
Version: 6.2.0.beta3
Release: 1%{dist}
Summary: Multi-WAN
License: GPLv3
Group: ClearOS/Apps
Source: %{name}-%{version}.tar.gz
Buildarch: noarch
Requires: %{name}-core = %{version}-%{release}
Requires: app-base
Requires: app-network

%description
Multi-WAN description...

%package core
Summary: Multi-WAN - APIs and install
License: LGPLv3
Group: ClearOS/Libraries
Requires: app-base-core
Requires: app-network-core
Requires: app-firewall-core
Requires: csplugin-routewatch
Requires: iptables
Requires: syswatch

%description core
Multi-WAN description...

This package provides the core API and libraries.

%prep
%setup -q
%build

%install
mkdir -p -m 755 %{buildroot}/usr/clearos/apps/multiwan
cp -r * %{buildroot}/usr/clearos/apps/multiwan/

install -D -m 0644 packaging/multiwan.conf %{buildroot}/etc/clearos/multiwan.conf
install -D -m 0644 packaging/routewatch-multiwan.conf %{buildroot}/etc/clearsync.d/routewatch-multiwan.conf

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
%config(noreplace) /etc/clearos/multiwan.conf
%config(noreplace) /etc/clearsync.d/routewatch-multiwan.conf

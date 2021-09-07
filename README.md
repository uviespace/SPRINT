# Space Project Ressource Integration Network Toolbox

The Space Project Ressource Integration Network Tool integrates following standards, resources and processes:

* ECSS (European Co-operation for Space Standardisation) Software Engineering standardization and processes: ECSS (https://ecss.nl/), Introducing ECSS Software-Engineering Standards within ESA (ESA bulletin 111 — august 2002) (https://www.esa.int/esapub/bulletin/bullet111/chapter21_bul111.pdf),
* SCOS2000 Mission Control System (e.g. run-time database): ESA - SCOS-2000 (https://www.esa.int/Enabling_Support/Operations/Ground_Systems_Engineering/SCOS-2000), MOC-applicable MIB egos-mcs-s2k-icd-0001-version7.0-FINAL.pdf (https://issues.cosmos.esa.int/solarorbiterwiki/download/attachments/23757668/MOC-applicable%20MIB%20egos-mcs-s2k-icd-0001-version7.0-FINAL.pdf?version=1&modificationDate=1522940882000&api=v2)
* EGS-CC (European Ground Systems Common Core) - common infrastructure to support space systems monitoring and control in pre- and post-launch phases for all mission types: ESA - EGS-CC (http://www.egscc.esa.int/)
* CORDET Framework from PnP Software including code generation functionality (see below)

## CORDET FW Editor

The CORDET FW Editor is a web-based tool to support the specification of a PUS-based system communication standard and of the applications which use it. The PUS ([Packet Utilization Standard](http://www.ecss.nl/wp-content/uploads/standards/ecss-e/ECSS-E-70-41A30Jan2003.pdf)) is an interface standard promoted by the European Space Agency for on-board applications.
The CORDET FW Editor allows a user to enter the specification information for a PUS-based system and to generate from it the following items:

* An Interface Control Document (ICD)
* A C-language component which implements the data pool for the applications in the PUS system
* A set of tables which specify the telecommands and telemetry reports in the PUS system and which can be imported in a specification document
* The configuration files to instantiate the [CORDET Framework](https://www.pnp-software.com/cordetfw/) for the applications in the PUS system

The help pages explains how to use the CORDET FW Editor. The editor is publicly accessible for registered users. Registration is free and only requires the user to enter a valid e-mail address. Local installations of the editor are available on a commercial basis from [P&P Software GmbH](https://www.pnp-software.com/).



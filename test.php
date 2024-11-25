<?php
IFELSE(
    model . max_loan_cap_grid < 300000,
    ROUNDOFF(MIN(input . Requested_loan_amount, (MIN(input . TPV * MIN(model . max_tenure, 6) *
    model . score_multipliers * model . thick_thin_multiplier, model . max_loan_cap_grid))), 1000),
    IFELSE(
        model . max_loan_cap_grid > 300000 && bureau . max_dpd_non_cc_last_12mo <= 7 && bureau . name ==
        "CIBIL" && bureau . score >= 750,
        ROUNDOFF(MIN(input . Requested_loan_amount, (MIN(input . TPV * MIN(model . max_tenure, 6) *
        model . score_multipliers * model . thick_thin_multiplier, model . max_loan_cap_grid))), 1000),
        IFELSE(
            model . max_loan_cap_grid > 300000 && bureau . max_dpd_non_cc_last_12mo >= 7 && bureau . name ==
            "CIBIL" && bureau . score <= 750,
            MIN(ROUNDOFF(MIN(input . Requested_loan_amount, (MIN(input . TPV * MIN(model . max_tenure, 6) *
            model . score_multipliers * model . thick_thin_multiplier, model . max_loan_cap_grid))), 1000), 300000),
            MIN(ROUNDOFF(MIN(input . Requested_loan_amount, (MIN(input . TPV * MIN(model . max_tenure, 6) *
            model . score_multipliers * model . thick_thin_multiplier, model . max_loan_cap_grid))), 1000), 300000)
        )
    )
)

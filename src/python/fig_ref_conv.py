#!/usr/bin/python

def fig_ref_conv(s, target):

    idx = -1
    while True:
        idx = s.find("#figure", idx + 1)
        if idx == -1:
            break

        idx_brace_open = s.find("(", idx)
        idx_comma_1st = s.find(",", idx_brace_open)
        idx_comma_2nd = s.find(",", idx_comma_1st + 1)
        idx_brace_close = s.find(")", idx_comma_2nd)

        # Check syntax of #figure directive
        if (idx_brace_open != idx + 7) or (idx_comma_1st == -1) or (idx_comma_2nd == -1) or (idx_brace_close == -1):
            print("Generator Error: incorrect #figure syntax in specification string: " + s + "\n")
            return ""

        url = s[idx_brace_open + 1:idx_comma_1st].strip()
        ref = s[idx_comma_1st + 1:idx_comma_2nd].strip()
        cap = s[idx_comma_2nd + 1:idx_brace_close].strip()

        if idx_brace_close != -1:
            if target == "doxy":
                sub = "\\image html {0} \"{1}\"".format(url, cap)
            elif target == "latex":
                sub = "\\ref{{fig:{0}}}".format(ref)
            elif target == "csv":
                sub = "#figure({0},{1},{2})".format(url, ref, cap)
            else:
                sub = ""

            s = s[:idx] + sub + s[idx_brace_close + 1:]

    return s
